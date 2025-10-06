<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('images/inq.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        label {
            color: #34495e;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        input[type="date"], textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="date"]:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        textarea {
            resize: vertical;
        }
        p {
            text-align: center;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Inquiry Form</h2>
        <form action="/submit-inquiry" method="POST">
            <label for="inquiry-date">Inquiry Date:</label>
            <input type="date" id="inquiry-date" name="inquiry-date" required><br>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="4" placeholder="Please enter your inquiry message here..." required></textarea><br>

            <button type="submit">Submit Inquiry</button>
        </form>
    </div>
</body>
</html>

