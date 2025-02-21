<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Списък за пазаруване</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ $shoppingList->name }}</h1>
    <p><strong>Собственик:</strong> {{ $shoppingList->user->name }}</p>
    <p><strong>Дата на създаване:</strong> {{ $shoppingList->created_at->format('d.m.Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Съставка</th>
                <th>Количество</th>
                <th>Закупено</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shoppingList->shoppingListItems as $item)
                <tr>
                    <td>{{ $item->ingredient->name }}</td>
                    <td>{{ $item->quantity_unit_text }}</td>
                    <td>{{ $item->is_purchased ? 'Да' : 'Не' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
