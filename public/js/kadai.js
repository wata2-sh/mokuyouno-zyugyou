<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mobile-Friendly Design</title>
  <style>
    /* ページ全体のスタイル */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }

    /* ヘッダーのスタイル */
    header {
      background-color: #333;
      color: #fff;
      text-align: center;
      padding: 1rem;
    }

    /* コンテンツ領域のスタイル */
    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 1rem;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* フォームのスタイル */
    .form-group {
      margin-bottom: 1rem;
    }

    .label {
      font-weight: bold;
      display: block;
      margin-bottom: 0.5rem;
    }

    .input {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    /* ボタンのスタイル */
    .button {
      background-color: #333;
      color: #fff;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    /* フッターのスタイル */
    footer {
      text-align: center;
      padding: 1rem;
      background-color: #333;
      color: #fff;
    }

    /* レスポンシブデザイン */
    @media (max-width: 768px) {
      .container {
        max-width: 100%;
        padding: 0.5rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Mobile-Friendly Design</h1>
  </header>
  <div class="container">
    <form>
      <div class="form-group">
        <label class="label" for="name">Name:</label>
        <input class="input" type="text" id="name" name="name">
      </div>
      <div class="form-group">
        <label class="label" for="email">Email:</label>
        <input class="input" type="email" id="email" name="email">
      </div>
      <button class="button" type="submit">Submit</button>
    </form>
  </div>
  <footer>
    &copy; 2023 Mobile-Friendly Design
  </footer>
</body>
</html>

