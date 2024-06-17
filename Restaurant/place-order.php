<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Table <span id="tableNumber"></span></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 400px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            color: #333;
        }
        .menu-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .menu-item:last-child {
            border-bottom: none;
        }
        .menu-item span {
            font-size: 18px;
        }
        .menu-item .quantity {
            display: flex;
            align-items: center;
        }
        .menu-item .quantity button {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
        }
        .menu-item .quantity input {
            width: 40px;
            padding: 5px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            margin: 0 5px;
        }
        .menu-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
        .order-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
        }
        .order-button:hover {
            background-color: #218838;
        }
    </style>
    <script>
        let menuItems = [];

        async function fetchMenu() {
            try {
                const response = await fetch('get_menu.php');
                menuItems = await response.json();
                let menuHtml = '';
                menuItems.forEach(item => {
                    menuHtml += `<div class="menu-item">
                                    <img src="burger_and_fries.jpg" alt="${item.name}">
                                    <span>${item.name} - $${item.price}</span>
                                    <div class="quantity">
                                        <button onclick="decrementQuantity(${item.id})">-</button>
                                        <input type="number" id="quantity_${item.id}" placeholder="0" min="0" value="0" onchange="updateTotal()">
                                        <button onclick="incrementQuantity(${item.id})">+</button>
                                    </div>
                                 </div>`;
                });
                document.getElementById('menu-items').innerHTML = menuHtml;
                updateTotal();
            } catch (error) {
                console.error('Error fetching menu:', error);
            }
        }

        function incrementQuantity(itemId) {
            const quantityInput = document.getElementById(`quantity_${itemId}`);
            quantityInput.value = parseInt(quantityInput.value) + 1;
            updateTotal();
        }

        function decrementQuantity(itemId) {
            const quantityInput = document.getElementById(`quantity_${itemId}`);
            const newValue = parseInt(quantityInput.value) - 1;
            quantityInput.value = newValue < 0 ? 0 : newValue;
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            menuItems.forEach(item => {
                const quantityInput = document.getElementById(`quantity_${item.id}`);
                const quantity = parseInt(quantityInput.value);
                total += item.price * quantity;
            });
            document.getElementById('total').textContent = `Total: $${total.toFixed(2)}`;
        }

        async function placeOrder(tableId) {
            const orderItems = [];
            menuItems.forEach(item => {
                const quantityInput = document.getElementById(`quantity_${item.id}`);
                const quantity = parseInt(quantityInput.value);
                if (quantity > 0) {
                    orderItems.push({ menu_item_id: item.id, quantity: quantity });
                }
            });

            if (orderItems.length === 0) {
                alert('Please select at least one item.');
                return;
            }

            try {
                const response = await fetch('place_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ table_id: tableId, order_items: orderItems })
                });
                const result = await response.json();
                if (response.ok) {
                    alert(result.message);
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error placing order:', error);
                alert('An error occurred while placing the order.');
            }
        }

        window.onload = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const tableId = urlParams.get('table');
            document.getElementById('tableNumber').textContent = tableId;
            fetchMenu();
            document.getElementById('orderButton').onclick = () => placeOrder(tableId);
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Menu - Table <span id="tableNumber"></span></h1>
        </div>
        <div id="menu-items"></div>
        <div class="total" id="total">Total: $0.00</div>
        <button class="order-button" id="orderButton">Order</button>
    </div>
</body>
</html>