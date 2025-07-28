# LifeBoard · 🗂 你的生活仪表盘

<div align="center">
  <a href="./README.md">English</a>｜<a href="./README.zh-CN.md">简体中文</a>
  <hr width="50%"/>
</div>

**LifeBoard** 是一个开源的个人生活管理仪表板，帮助你跟踪、可视化和管理日常活动、习惯、情绪与灵感。它是一个可自托管的平台，让你能够在本地或自己的服务器上集中管理各种生活数据，并以模块化、可定制的方式展示。

LifeBoard 聚焦于三大核心方向：

- **习惯养成**：跟踪日常行为，培养长期坚持的好习惯
- **自我管理**：组织任务、监控生产力，管理你的个人信息
- **灵感收集**：以结构化方式记录想法、反思和灵感片段

可以把 **LifeBoard** 想象成一个个人生活的“控制中心”。它结合了 Notion、Obsidian、Todoist 等工具的优点，又提供完整的数据所有权和隐私保护。

**本项目已经经由 Zread 解析完成，如果需要快速了解项目，可以点击此处进行查看：[了解本项目](https://zread.ai/zxc7563598/life-board)**

---

## ✨ 核心功能

|功能|描述|
| ------| --------------------------------------------------|
|**模块化界面**|使用可自由添加、移除和排列的「模块卡片」组织内容|
|**数据可视化**|用交互式图表和热力图查看习惯趋势、个人指标|
|**私有部署**|自托管，确保个人数据 100% 属于你自己|

---

## 🖼 项目截图

### 📌 登陆/注册 
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/login.png">

### 📌 首页
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/home1.png">
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/home2.png">

### 📌 财务管理模块
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/bill1.png">
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/bill2.png">

### 📌 任务列表
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/todo1.png">
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/todo2.png">

---

## ✅ 已上线模块

- **财务管理**：支持邮箱监听，自动解析支付宝/微信交易邮件，生成财务图表报告，帮你追踪收入和消费趋势
- **任务列表**：轻量级待办工具，支持标签、日期和完成状态，并提供日历视图
- **快速搜索**：一键搜索百度、Google、B站、知乎等平台
- **热点新闻**：整合主流新闻/短视频平台热点，支持快速查看与跳转

---

## 🚀 开发中模块

- **每日签到**：跟踪饮水、阅读、锻炼、番茄时钟等活动，生成周/月热力图
- **情绪追踪**：记录每日情绪，支持表情打分和趋势分析
- **日记与想法**：Markdown 记录支持媒体嵌入，自动按时间线整理
- **健康管理**：接入智能设备数据，展示长期健康趋势

---

## 📂 项目结构

```
life-board/
├── backend/         # PHP Webman 框架
├── frontend/        # Vue 3 应用
└── [配置文件]       # 根目录配置
```

后端基于 **Webman** 框架，前端采用 **Vue 3**。部署需要以下环境：

- PHP 8.1+
- MySQL 8+
- Redis

### 必需 PHP 扩展

​`event`​、`redis`​、`imap`​

---

## ⚙️ 部署说明

### 后端

1️⃣ 克隆仓库并进入后端目录

```bash
git clone https://github.com/zxc7563598/life-board.git
cd life-board/backend
```

2️⃣ 复制并修改 `.env`​ 文件

```bash
cp .env.example .env
```

3️⃣ 安装 PHP 依赖

```bash
composer install
```

4️⃣ 初始化数据库

```bash
php vendor/bin/phinx migrate
```

5️⃣ 启动后端

```bash
php start.php start
```

---

### 前端

前端是标准的 npm 项目，步骤同样简单。

1️⃣ 进入前端目录（如果已经克隆过仓库可跳过 git clone）

```bash
cd life-board/frontend
```

2️⃣ 复制并修改 `.env`​ 文件

```bash
cp .env.example .env
```

3️⃣ 安装依赖

```bash
npm install
```

4️⃣ 运行开发环境

```bash
npm run dev
```

5️⃣ 生产环境打包

```bash
npm run build
# 打包产物在 frontend/dist
```
