<?php

class Product
{

    private function getOrdersQuantity($productId)
    {
        return OrderLine::find()->select('SUM(quantity) as quantity')
            ->joinWith('order')
            ->where("(order.status = '" . Order::STATUS_PENDING . "' OR order.status = '" . Order::STATUS_PROCESSING . "' OR order.status = '" . Order::STATUS_WAITING_ACCEPTANCE . "') AND order_line.product_id = $productId")
            ->scalar();
    }

    private function getBlockedStockQuantity($productId)
    {
        return BlockedStock::find()->select('SUM(quantity) as quantity')
            ->joinWith('shoppingCart')
            ->where("blocked_stock.product_id = $productId AND blocked_stock_date > '" . date('Y-m-d H:i:s') . "' AND (shopping_cart_id IS NULL OR shopping_cart.status = '" . ShoppingCart::STATUS_PENDING . "')")
            ->scalar();
    }

    private function calculateQuantity($securityStockConfig, $quantity)
    {
        if (! empty($securityStockConfig)) {
            $quantity = ShopChannel::applySecurityStockConfig($quantity, @$securityStockConfig->mode, @$securityStockConfig->quantity);
        }
        return $quantity > 0 ? $quantity : 0;
    }

    public static function stock($productId, $quantityAvailable, $cache = false, $cacheDuration = 60, $securityStockConfig = null)
    {
        // Si tenemos cache la usamos
        if ($cache) {
            // Obtenemos el stock bloqueado por pedidos en curso
            $ordersQuantity = OrderLine::getDb()->cache(function ($db) use ($productId) {
                return self::getOrdersQuantity($productId);
            }, $cacheDuration);

            // Obtenemos el stock bloqueado
            $blockedStockQuantity = BlockedStock::getDb()->cache(function ($db) use ($productId) {
                return self::getBlockedStockQuantity($productId);
            }, $cacheDuration);
        } else {
            // Obtenemos el stock bloqueado por pedidos en curso
            $ordersQuantity = self::getOrdersQuantity($productId);

            // Obtenemos el stock bloqueado
            $blockedStockQuantity = self::getBlockedStockQuantity($productId);
        }

        // Calculamos las unidades disponibles
        if ($quantityAvailable >= 0) {
            if (isset($ordersQuantity) || isset($blockedStockQuantity)) {
                $quantity = $quantityAvailable - @$ordersQuantity - @$blockedStockQuantity;
                return self::calculateQuantity($securityStockConfig, $quantity);
            } else {
                return self::calculateQuantity($securityStockConfig, $quantityAvailable);
            }
        } else {
            return $quantityAvailable;
        }

        return 0;
    }
}

