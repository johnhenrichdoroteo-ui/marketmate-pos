<?php

use CodeIgniter\Test\CIUnitTestCase;

/** @internal */
final class MarketMateProductImagesTest extends CIUnitTestCase
{
    public function testBundledProductPhotoResolvesToItsAsset(): void
    {
        helper('marketmate');

        $this->assertSame(
            base_url('assets/images/products/wireless-mouse.jpg'),
            marketmate_product_image_url('wireless-mouse.jpg')
        );
    }

    public function testAdditionalSamplePhotosResolveToTheirAssets(): void
    {
        helper('marketmate');

        foreach ([
            '7-port-usb-hub.jpg',
            'over-ear-wireless-headphones.jpg',
            'laptop-sleeve-14-inch.jpg',
            'portable-monitor-15-6-inch.jpg',
            'usb-c-docking-station.jpg',
            'smart-desk-lamp.jpg',
            'wireless-presenter.jpg',
            '4k-hdmi-cable.jpg',
            '2tb-external-hard-drive.jpg',
            'rechargeable-desk-fan.jpg',
        ] as $filename) {
            $this->assertSame(
                base_url('assets/images/products/' . $filename),
                marketmate_product_image_url($filename)
            );
        }
    }

    public function testMissingOrUnsafeFilenameUsesPlaceholder(): void
    {
        helper('marketmate');

        $placeholder = base_url('assets/images/product-placeholder.svg');
        $this->assertSame($placeholder, marketmate_product_image_url(null));
        $this->assertSame($placeholder, marketmate_product_image_url('../wireless-mouse.jpg'));
        $this->assertSame($placeholder, marketmate_product_image_url('not-in-the-catalog.jpg'));
    }
}
