<?php

namespace app\process;

use app\model\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidTimeZoneException;
use Exception;
use Workerman\Crontab\Crontab;
use Webman\RedisQueue\Client;
use resource\enums\UserEnums;

class Task
{
    public function onWorkerStart()
    {
        // 每天的0点执行
        new Crontab('0 0 * * *', function () {});
        // 每5分钟执行一次
        new Crontab('0 */5 * * * *', function () {
            sublog('定时任务', '账单队列', '开始执行', []);
            $user = User::where('status', UserEnums\Status::Enable->value)->get([
                'id' => 'id',
                'nickname' => 'nickname',
                'mail_host' => 'mail_host',
                'mail_username' => 'mail_username',
                'mail_password' => 'mail_password',
            ]);
            foreach ($user as $_user) {
                if (!empty($_user->mail_host) && !empty($_user->mail_username) && !empty($_user->mail_password)) {
                    sublog('定时任务', '账单队列', '用户开始获取数据', $_user->toArray());
                    $mail_host = "{{$_user->mail_host}}INBOX";
                    $downloads = self::fetchBillAttachmentsFromMailbox($mail_host, $_user->mail_username, $_user->mail_password);
                    sublog('定时任务', '账单队列', '获取到数据', $downloads);
                    if (!empty($downloads['alipay'])) {
                        foreach ($downloads['alipay'] as $path) {
                            Client::send('alipay-bill-parser', [
                                'user_id' => $_user->id,
                                'path' => $path
                            ]);
                            sublog('定时任务', '账单队列', '投递到支付宝队列', ['user_id' => $_user->id, 'path' => $path]);
                        }
                    }
                    if (!empty($downloads['wechat'])) {
                        foreach ($downloads['wechat'] as $path) {
                            Client::send('wechat-bill-parser', [
                                'user_id' => $_user->id,
                                'path' => $path
                            ]);
                            sublog('定时任务', '账单队列', '投递到微信队列', ['user_id' => $_user->id, 'path' => $path]);
                        }
                    }
                }
            }
        });
    }

    /**
     * 连接指定邮箱，读取未读邮件中的支付宝和微信账单邮件，
     * 并调用对应方法提取账单附件（ZIP 文件）保存到本地，最终返回下载结果。
     *
     * 邮件来源：
     * - 微信账单邮件：wechatpay@tencent.com
     * - 支付宝账单邮件：service@mail.alipay.com
     *
     * 检索方式：
     * - 搜索所有未读邮件（UNSEEN）
     * - 从未读中分别筛选微信和支付宝邮件
     * - 分别调用对应处理方法提取附件
     *
     * @param string $mailbox  IMAP 邮箱连接字符串，例如：`{imap.gmail.com:993/imap/ssl}INBOX`
     * @param string $username 邮箱用户名（完整邮箱地址）
     * @param string $password 邮箱密码（或授权码）
     *
     * @return array
     */
    private static function fetchBillAttachmentsFromMailbox($mailbox, $username, $password): array
    {
        // 初始化返回信息
        $result = [
            'open' => true,
            'wechat' => [],
            'alipay' => [],
        ];
        // 连接邮件服务器
        try {
            $inbox = @imap_open($mailbox, $username, $password);
        } catch (\Exception $e) {
            $inbox = false;
        }
        if ($inbox === false) {
            $result['open'] = false;
            return $result;
        }
        // 连接成功，获取压缩包下载链接
        $unseen = imap_search($inbox, 'UNSEEN', SE_UID) ?: [];
        $wechat = imap_search($inbox, 'FROM "wechatpay@tencent.com"', SE_UID) ?: [];
        $alipay = imap_search($inbox, 'FROM "service@mail.alipay.com"', SE_UID) ?: [];
        $wechatUnread = array_intersect($unseen, $wechat);
        $alipayUnread = array_intersect($unseen, $alipay);
        if (!empty($alipayUnread)) {
            $result['alipay'] = self::fetchAlipayBillAttachments($inbox, $alipayUnread);
        }
        if (!empty($wechatUnread)) {
            $result['wechat'] = self::fetchWeChatBillAttachments($inbox, $wechatUnread);
        }
        imap_close($inbox);
        return $result;
    }

    /**
     * 从指定的 IMAP 邮箱连接中，按照给定的邮件 UID 列表提取支付宝账单附件（zip 文件），并保存到本地目录中。
     *
     * 该方法会：
     * - 遍历邮件 UID（倒序，优先处理最新邮件）
     * - 提取邮件中第二部分（一般为附件）的结构与文件名
     * - 对附件内容进行 base64 解码
     * - 保存为本地 zip 文件，并返回保存路径列表
     * 
     * 保存路径：public/bill/alipay/YYYYMMDDHHMMSS/目录下
     *
     * @param mixed $inbox 已连接的 IMAP 邮箱资源
     * @param array $uids 邮件的 UID 列表
     *
     * @return array
     */
    private static function fetchAlipayBillAttachments($inbox, array $uids): array
    {
        $attachments = [];
        rsort($uids);
        foreach ($uids as $uid) {
            $structure = imap_fetchstructure($inbox, $uid, FT_UID);
            $filename = 'unknown.zip';
            if (isset($structure->parts[1]->dparameters)) {
                foreach ($structure->parts[1]->dparameters as $param) {
                    $decoded = imap_mime_header_decode($param->value);
                    if ($decoded) {
                        $filename = '';
                        foreach ($decoded as $part) {
                            $filename .= $part->text;
                        }
                    }
                }
            }
            $body = base64_decode(imap_fetchbody($inbox, $uid, '2', FT_UID));
            $directory = public_path('bill/alipay/' . Carbon::now()->timezone(config('app.default_timezone'))->format('YmdHis'));
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0755, true)) {
                    throw new \Exception("无法创建目标目录: " . $directory);
                }
            }
            $filePath = $directory . '/' . $filename;
            file_put_contents($filePath, $body);
            $attachments[] = $filePath;
        }
        return $attachments;
    }

    /**
     * 从指定的 IMAP 邮箱中，提取微信账单邮件中的下载链接，拉取 zip 附件并保存到本地目录。
     *
     * 处理流程：
     * - 遍历邮件 UID（倒序，优先处理最新邮件）
     * - 解析 HTML 邮件正文，匹配微信账单下载链接（含 token）
     * - 使用 cURL 下载 zip 文件
     * - 解出文件名，并保存为本地文件
     *
     * 保存路径：public/bill/wechat/YYYYMMDDHHMMSS/目录下
     *
     * @param mixed $inbox 已连接的 IMAP 邮箱资源
     * @param array $uids 邮件的 UID 列表
     *
     * @return array
     */
    private static function fetchWeChatBillAttachments($inbox, array $uids): array
    {
        $attachments = [];
        rsort($uids);
        foreach ($uids as $uid) {
            $htmlBody = imap_fetchbody($inbox, $uid, '2', FT_UID);
            $htmlDecoded = base64_decode($htmlBody);
            if (preg_match('#https://tenpay\.wechatpay\.cn/userroll/userbilldownload/downloadfilefromemail\?[^"\']+#', $htmlDecoded, $matches)) {
                $url = html_entity_decode($matches[0]);
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HEADER => true,
                ]);
                $response = curl_exec($ch);
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpCode !== 200 || $response === false) {
                    continue;
                }
                $body = substr($response, $headerSize);
                $filename = 'downloaded_' . date('Ymd_His') . '.zip';
                $directory = public_path('bill/wechat/' . Carbon::now()->timezone(config('app.default_timezone'))->format('YmdHis'));
                if (!is_dir($directory)) {
                    if (!mkdir($directory, 0755, true)) {  // 尝试递归创建目录
                        throw new \Exception("无法创建目标目录: " . $directory);
                    }
                }
                $fullPath = $directory . '/' . $filename;
                file_put_contents($fullPath, $body);
                $attachments[] = $fullPath;
            }
        }
        return $attachments;
    }
}
