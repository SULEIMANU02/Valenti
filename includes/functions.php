<?php
/**
 * Valenti Atelier - Core Utility Functions
 */

require_once dirname(__DIR__) . '/config/config.php';

function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function formatPrice(float $amount): string {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// Flash Messaging System
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message
    ];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Cart Session Management
function initCart(): void {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function getCart(): array {
    initCart();
    return $_SESSION['cart'];
}

function addToCart(int $productId, int $qty, string $size = 'M', string $color = 'Default'): void {
    initCart();
    $cartKey = "{$productId}_{$size}_{$color}";
    
    if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['quantity'] += $qty;
    } else {
        $_SESSION['cart'][$cartKey] = [
            'product_id' => $productId,
            'quantity' => $qty,
            'size' => $size,
            'color' => $color
        ];
    }
}

function updateCart(string $cartKey, int $qty): void {
    initCart();
    if ($qty <= 0) {
        unset($_SESSION['cart'][$cartKey]);
    } else if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['quantity'] = $qty;
    }
}

function removeFromCart(string $cartKey): void {
    initCart();
    unset($_SESSION['cart'][$cartKey]);
}

function clearCart(): void {
    $_SESSION['cart'] = [];
    unset($_SESSION['applied_coupon']);
}

function getCartCount(): int {
    $cart = getCart();
    $count = 0;
    foreach ($cart as $item) {
        $count += (int)$item['quantity'];
    }
    return $count;
}

function calculateCartTotals(PDO $pdo): array {
    $cart = getCart();
    $subtotal = 0.0;
    $items = [];

    if (!empty($cart)) {
        $ids = array_unique(array_column($cart, 'product_id'));
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute(array_values($ids));
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $productMap = [];
            foreach ($products as $p) {
                $productMap[$p['id']] = $p;
            }

            foreach ($cart as $key => $item) {
                $pid = $item['product_id'];
                if (isset($productMap[$pid])) {
                    $p = $productMap[$pid];
                    $effectivePrice = ($p['sale_price'] !== null && $p['sale_price'] > 0) ? (float)$p['sale_price'] : (float)$p['price'];
                    $itemTotal = $effectivePrice * (int)$item['quantity'];
                    $subtotal += $itemTotal;

                    $items[$key] = [
                        'key' => $key,
                        'product_id' => $pid,
                        'name' => $p['name'],
                        'slug' => $p['slug'],
                        'price' => $effectivePrice,
                        'original_price' => (float)$p['price'],
                        'image' => $p['image'],
                        'size' => $item['size'],
                        'color' => $item['color'],
                        'quantity' => (int)$item['quantity'],
                        'total' => $itemTotal,
                        'max_stock' => (int)$p['stock']
                    ];
                }
            }
        }
    }

    // Coupon calculation
    $discount = 0.0;
    $couponCode = null;
    if (isset($_SESSION['applied_coupon'])) {
        $coupon = $_SESSION['applied_coupon'];
        if ($subtotal >= $coupon['min_spend']) {
            $couponCode = $coupon['code'];
            if ($coupon['type'] === 'percentage') {
                $discount = ($subtotal * $coupon['value']) / 100.0;
            } else {
                $discount = min($subtotal, (float)$coupon['value']);
            }
        } else {
            unset($_SESSION['applied_coupon']);
        }
    }

    $discountedSubtotal = max(0.0, $subtotal - $discount);
    
    // Shipping calculation
    $shipping = 0.0;
    if ($discountedSubtotal > 0) {
        $shipping = ($discountedSubtotal >= FREE_SHIPPING_THRESHOLD) ? 0.0 : FLAT_SHIPPING_RATE;
    }

    // Tax calculation
    $tax = round($discountedSubtotal * TAX_RATE, 2);
    $total = round($discountedSubtotal + $shipping + $tax, 2);

    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'coupon_code' => $couponCode,
        'shipping' => $shipping,
        'tax' => $tax,
        'total' => $total
    ];
}

function renderStars(int $rating): string {
    $rating = max(1, min(5, $rating));
    $html = '<div class="rating-stars" aria-label="' . $rating . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<span class="star filled">&#9733;</span>';
        } else {
            $html .= '<span class="star empty">&#9734;</span>';
        }
    }
    $html .= '</div>';
    return $html;
}
