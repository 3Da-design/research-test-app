<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blade Hello</title>
</head>
<body>
    <h1>Blade Screen</h1>
    <p>{{ $message }}</p>

    <h2>Items</h2>
    <ul>
        @foreach ($items as $item)
            <li>{{ $item['id'] }}: {{ $item['name'] }}</li>
        @endforeach
    </ul>
</body>
</html>
