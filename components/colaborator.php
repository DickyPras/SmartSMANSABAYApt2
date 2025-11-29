<?php
// Tentukan path relatif yang benar dari lokasi file yang meng-include
// File ini di-include dari views/user/auth/, jadi perlu naik 3 level
$imagePath = '../../../public/images/'; 

// Atau gunakan path absolut dari root web
// $imagePath = '/SmartSMANSABAYApt2/public/images/';
?>

<div class="mt-12">
    <div class="flex justify-center space-x-6">

        <div class="w-12 h-12 rounded-full border-4 border-white shadow-lg flex items-center justify-center bg-white">
            <img src="<?php echo $imagePath . 'Logo-Smansabaya.png'; ?>" 
                 alt="Logo Smansabaya" 
                 class="w-8 h-8 object-contain"
                 onerror="console.log('Failed to load: <?php echo $imagePath . 'Logo-Smansabaya.png'; ?>')">
        </div>

        <div class="w-12 h-12 rounded-full border-4 border-white shadow-lg flex items-center justify-center bg-white">
            <img src="<?php echo $imagePath . 'Logo-Esmart.png'; ?>" 
                 alt="Logo Esmart" 
                 class="w-8 h-8 object-contain"
                 onerror="console.log('Failed to load: <?php echo $imagePath . 'Logo-Esmart.png'; ?>')">
        </div>

    </div>
</div>