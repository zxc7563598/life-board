# LifeBoard · 🗂 你的生活仪表盘

<div align="center">
  <a href="./README.md">English</a>｜<a href="./README.zh-CN.md">简体中文</a>
  <hr width="50%"/>
</div>

**LifeBoard** is an open-source personal life management dashboard that assists you in tracking, visualizing, and managing daily activities, habits, emotions, and inspirations. It serves as a self-hosted platform, enabling you to centrally manage various life data locally or on your own server, and present it in a modular and customizable manner.

LifeBoard focuses on three core directions:

- **Habit formation**：Tracking daily behaviors and cultivating good habits that can be sustained over the long term
- **Self-management**：Organize tasks, monitor productivity, and manage your personal information
- **Inspiration collection**：Record ideas, reflections, and inspiration fragments in a structured manner

You can think of **LifeBoard** as a "control center" for personal life. It combines the advantages of tools such as Notion, Obsidian, and Todoist, while also providing complete data ownership and privacy protection.

**This project has been parsed by Zread. If you need a quick overview of the project, you can click here to view it：[Understand this project](https://zread.ai/zxc7563598/life-board)**

---

## ✨ Core functions

|function|describe|
| ------| --------------------------------------------------|
|**Modular interface**|Organize content using "module cards" that can be freely added, removed, and arranged|
|**Data visualization**|View habit trends and personal metrics using interactive charts and heat maps|
|**Private deployment**|Self-custody ensures that your personal data is 100% yours|

---

## 🖼 Project screenshot

### 📌 Login / Register
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/login.png">

### 📌 Home page
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/home1.png">
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/home2.png">

### 📌 Financial management module
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/bill1.png">
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/bill2.png">

### 📌 Task list
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/todo1.png">
<img src="https://raw.githubusercontent.com/zxc7563598/life-board/main/backend/public/screenshot/todo2.png">

---

## ✅ Modules already online

- **Financial management**：Supports email monitoring, automatically parses Alipay/WeChat transaction emails, generates financial chart reports, and helps you track income and spending trends
- **Task list**：A lightweight to-do tool that supports tags, dates, and completion statuses, and provides a calendar view
- **Quick Search**：One-click search on platforms such as Baidu, Google, Bilibili, Zhihu, and more
- **Hot News**：Integrate hot topics from mainstream news and short video platforms, supporting quick viewing and navigation

---

## 🚀 Module under development

- **Daily check-in**：Track activities such as drinking water, reading, exercising, and using the Pomodoro technique, and generate weekly/monthly heat maps
- **Emotional tracking**：Record daily emotions, support facial expression scoring and trend analysis
- **Diary and Thoughts**：Markdown records support media embedding and are automatically organized in a timeline
- **Health management**：Integrate data from smart devices to display long-term health trends

---

## 📂 Project Structure

```
life-board/
├── backend/                 # PHP Webman Framework
├── frontend/                # Vue 3 application
└── [Configuration File]     # Root Directory Configuration
```

The backend is built on the **Webman** framework, while the frontend utilizes **Vue 3**. The deployment requires the following environment：

- PHP 8.1+
- MySQL 8+
- Redis

### Required PHP extensions

​`event`​、`redis`​、`imap`​

---

## ⚙️ Deployment instructions

### Backend

1️⃣ Clone the repository and enter the backend directory

```bash
git clone https://github.com/zxc7563598/life-board.git
cd life-board/backend
```

2️⃣ Copy and modify the `.env` file

```bash
cp .env.example .env
```

3️⃣ Install PHP dependencies

```bash
composer install
```

4️⃣ Initialize the database

```bash
php vendor/bin/phinx migrate
```

5️⃣ Start backend

```bash
php start.php start
```

---

### front end

The frontend is a standard npm project, and the steps are equally simple.

1️⃣ Enter the frontend directory (if you have already cloned the repository, you can skip git clone)

```bash
cd life-board/frontend
```

2️⃣ Copy and modify the `.env` file

```bash
cp .env.example .env
```

3️⃣ Install dependencies

```bash
npm install
```

4️⃣ Run the development environment

```bash
npm run dev
```

5️⃣ Packaging for production environment

```bash
npm run build
# The bundled artifacts are in frontend/dist
```
