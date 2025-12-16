<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 414px;
            margin: 0 auto;
            background-color: #fff;
            min-height: 100vh;
            padding-bottom: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #fff;
            border-bottom: 1px solid #e0e0e0;
        }

        .header-icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFD700;
            border-radius: 50%;
            color: #FFD700;
            font-size: 18px;
            cursor: pointer;
        }

        .header-title {
            font-size: 18px;
            font-weight: 600;
            color: #FFD700;
        }

        .product-section {
            padding: 30px 20px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .product-name {
            text-align: center;
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }

        .rating {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            background-color: #FFD700;
            padding: 8px 15px;
            border-radius: 20px;
            width: fit-content;
            margin: 0 auto 15px;
        }

        .star {
            color: #fff;
            font-size: 16px;
        }

        .rating-text {
            color: #fff;
            font-size: 12px;
            font-weight: 500;
        }

        .price {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #FFD700;
            margin-bottom: 20px;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 10px;
            transition: opacity 0.3s;
        }

        .btn:active {
            opacity: 0.8;
        }

        .btn-primary {
            background-color: #FFD700;
            color: #333;
        }

        .btn-secondary {
            background-color: #FFD700;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">←</div>
            <div class="header-title">Beli</div>
            <div class="header-icon">🔍</div>
        </div>

        <div class="product-section">
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1594787318286-3d835c1d207f?w=400" alt="Keripik Buah" class="product-image">
                
                <p class="product-name">Keisst lembut dari kain perca</p>
                
                <div class="rating">
                    <span class="star">⭐</span>
                    <span class="star">⭐</span>
                    <span class="star">⭐</span>
                    <span class="star">⭐</span>
                    <span class="star">⭐</span>
                </div>

                <div class="price">Rp 25.000</div>

                <button class="btn btn-primary">beli</button>
                <button class="btn btn-secondary">Tambahkan ke keranjang</button>
            </div>
        </div>
    </div>
</body>
</html>