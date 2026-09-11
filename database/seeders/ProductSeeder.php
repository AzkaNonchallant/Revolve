<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed kategori beserta daftar produk.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sepatu',
                'products' => [
                    ['name' => 'Sepatu Sneakers Casual White', 'description' => "Sneakers casual warna putih dengan desain minimalis yang cocok untuk aktivitas harian.\n\nUpper kulit sintetis premium, sol karet anti selip, dan insole empuk untuk kenyamanan seharian.", 'nutrition' => "Komposisi Bahan\n\nUpper: kulit sintetis premium\nSol: karet EVA\nInsole: busa memory foam", 'price' => 349000, 'stock' => 40, 'weight' => 800, 'healthy_score' => 70],
                    ['name' => 'Sepatu Lari Running', 'description' => "Sepatu lari dengan teknologi bantalan responsif untuk kenyamanan saat berlari.\n\nRingan, breathable, dan dirancang untuk meredam benturan.", 'nutrition' => "Komposisi Bahan\n\nUpper: mesh breathable\nSol: midsole EVA + outsole karet\nBerat: 260 gr (size 42)", 'price' => 399000, 'stock' => 35, 'weight' => 720, 'healthy_score' => 72],
                    ['name' => 'Sepatu Kulit Formal', 'description' => "Sepatu formal berbahan kulit asli dengan jahitan rapi untuk acara resmi.\n\nCocok untuk kantor, meeting, dan acara formal lainnya.", 'nutrition' => "Komposisi Bahan\n\nUpper: kulit sapi asli\nInsole: kulit kambing\nSol: karet antislip", 'price' => 575000, 'stock' => 25, 'weight' => 900, 'healthy_score' => 68],
                    ['name' => 'Sepatu Kanvas Retro', 'description' => "Sepatu kanvas gaya retro dengan motif klasik yang timeless.\n\nRingan dan nyaman, pasangankan dengan berbagai outfit kasual.", 'nutrition' => "Komposisi Bahan\n\nUpper: kanvas katun\nSol: karet vulkanisir\nInsole: tekstil lembut", 'price' => 189000, 'stock' => 60, 'weight' => 650, 'healthy_score' => 66],
                    ['name' => 'Sandal Kulit Pria', 'description' => "Sandal kulit pria dengan gesper klasik untuk kesan santai tapi tetap rapi.\n\nTampilan elegan dengan daya tahan kulit asli.", 'nutrition' => "Komposisi Bahan\n\nUpper: kulit sapi asli\nSol: karet tebal\nLapisan: kulit sintetis", 'price' => 215000, 'stock' => 45, 'weight' => 500, 'healthy_score' => 65],
                ],
            ],
            [
                'name' => 'Baju',
                'products' => [
                    ['name' => 'Kaos Polos Cotton Combed', 'description' => "Kaos polos berbahan cotton combed 30s yang adem dan lembut di kulit.\n\nJahitan rapi dan tidak mudah melar, cocok untuk daily wear.", 'nutrition' => "Komposisi Bahan\n\n100% katun combed 30s\nGramasi 140 gsm\nTidak melar setelah dicuci", 'price' => 85000, 'stock' => 150, 'weight' => 200, 'healthy_score' => 74],
                    ['name' => 'Kaos Polo Premium', 'description' => "Polo shirt premium dengan kerah dan kancing original untuk tampilan rapi kasual.\n\nCocok untuk santai maupun semi-formal.", 'nutrition' => "Komposisi Bahan\n\n100% katun pique\nGramasi 220 gsm\nKerah Peter Pan reinforced", 'price' => 129000, 'stock' => 90, 'weight' => 250, 'healthy_score' => 73],
                    ['name' => 'T-Shirt Oversize', 'description' => "T-shirt oversized dengan potongan longgar untuk gaya streetwear.\n\nBahan heavyweight yang jatuh dan tidak menerawang.", 'nutrition' => "Komposisi Bahan\n\n100% katun carded\nGramasi 230 gsm\nPotongan boxy fit", 'price' => 99000, 'stock' => 120, 'weight' => 280, 'healthy_score' => 72],
                    ['name' => 'Kemeja Flanel Pria', 'description' => "Kemeja flanel kotak-kotak hangat untuk gaya kasual maupun layering.\n\nBahan lembut, adem, dan nyaman dipakai seharian.", 'nutrition' => "Komposisi Bahan\n\n60% katun, 40% poliester\nFleece ringan\nKancing depan penuh", 'price' => 159000, 'stock' => 70, 'weight' => 350, 'healthy_score' => 71],
                    ['name' => 'Kemeja Oxford', 'description' => "Kemeja oxford klasik berwarna dasar netral untuk kesan rapi dan profesional.\n\nBahan oxford dengan tekstur khas yang mudah disetrika.", 'nutrition' => "Komposisi Bahan\n\n100% katun oxford\nGramasi 150 gsm\nKerah button-down", 'price' => 199000, 'stock' => 65, 'weight' => 300, 'healthy_score' => 70],
                    ['name' => 'Batik Modern Pria', 'description' => "Kemeja batik modern dengan motif kontemporer untuk acara semi-formal.\n\nPerpaduan batik tradisional dan potongan masa kini.", 'nutrition' => "Komposisi Bahan\n\n100% katun premium\nBatik cap dan tulis\nLengan reguler", 'price' => 185000, 'stock' => 50, 'weight' => 280, 'healthy_score' => 69],
                ],
            ],
            [
                'name' => 'Jaket',
                'products' => [
                    ['name' => 'Jaket Hoodie Premium', 'description' => "Hoodie premium dengan bahan fleece tebal dan hood dengan tali serut.\n\nHangat, lembut, dan cocok untuk santai atau layering.", 'nutrition' => "Komposisi Bahan\n\n60% katun, 40% poliester\nFleece tebal 300 gsm\nKangaroo pocket besar", 'price' => 249000, 'stock' => 80, 'weight' => 700, 'healthy_score' => 72],
                    ['name' => 'Jaket Bomber', 'description' => "Jaket bomber klasik dengan resleting depan dan ribbing di kerah, manset, dan bawah.\n\nGaya timeless yang cocok untuk berbagai kesempatan.", 'nutrition' => "Komposisi Bahan\n\nOuter: poliester tafta\nLining: mesh\nRibbing: katun rib", 'price' => 289000, 'stock' => 55, 'weight' => 650, 'healthy_score' => 71],
                    ['name' => 'Jaket Denim', 'description' => "Jaket denim klasik dengan kancing logam yang awet dan stylist.\n\nSemakin sering dipakai semakin khas warnanya.", 'nutrition' => "Komposisi Bahan\n\n100% denim katun\nKetebalan 12 oz\nKancing alloy original", 'price' => 325000, 'stock' => 42, 'weight' => 1100, 'healthy_score' => 70],
                    ['name' => 'Jaket Windbreaker', 'description' => "Jaket windbreaker ringan dan tahan angin yang mudah dilipat untuk dibawa bepergian.\n\nCocok untuk hiking, bersepeda, dan aktivitas outdoor.", 'nutrition' => "Komposisi Bahan\n\nOuter: nylon ripstop waterproof\nLining: mesh breathable\nDapat dilipat menjadi pouch", 'price' => 219000, 'stock' => 95, 'weight' => 380, 'healthy_score' => 73],
                    ['name' => 'Jaket Parka Musim Dingin', 'description' => "Jaket parka tebal dengan hood berbulu untuk melindungi dari udara dingin ekstrem.\n\nIsolasi hangat dengan pelapis tahan air.", 'nutrition' => "Komposisi Bahan\n\nOuter: poliester waterproof\nIsolasi: holofiber 220 gsm\nInner: softshell fleece", 'price' => 459000, 'stock' => 30, 'weight' => 1400, 'healthy_score' => 75],
                ],
            ],
            [
                'name' => 'Celana',
                'products' => [
                    ['name' => 'Celana Jeans Slim Fit', 'description' => "Celana jeans slim fit dengan bahan denim stretch yang nyaman dipakai seharian.\n\nPotongan modern untuk tampilan rapi dan casual.", 'nutrition' => "Komposisi Bahan\n\n98% denim katun, 2% spandex\nKetebalan 11.5 oz\nWash vintage", 'price' => 275000, 'stock' => 75, 'weight' => 750, 'healthy_score' => 71],
                    ['name' => 'Celana Chino', 'description' => "Celana chino berbahan twill halus untuk gaya kasual hingga semiformal.\n\nWarna netral yang mudah dipadukan dengan berbagai atasan.", 'nutrition' => "Komposisi Bahan\n\n97% katun twill, 3% spandex\nGramasi 240 gsm\nRegular fit", 'price' => 229000, 'stock' => 85, 'weight' => 650, 'healthy_score' => 70],
                    ['name' => 'Celana Joger', 'description' => "Celana joger santai dengan bahan cotton fleksibel dan kantong fungsional.\n\nNyaman untuk aktivitas harian dan santai.", 'nutrition' => "Komposisi Bahan\n\n95% katun fleksibel, 5% spandex\nGramasi 210 gsm\nRib ankle", 'price' => 155000, 'stock' => 110, 'weight' => 550, 'healthy_score' => 69],
                    ['name' => 'Celana Pendek Cargo', 'description' => "Celana pendek cargo dengan banyak kantong untuk kebutuhan praktis kamu.\n\nBahan tebal tapi tetap adem untuk aktivitas outdoor.", 'nutrition' => "Komposisi Bahan\n\n100% katun drill\nGramasi 250 gsm\nKantong kargo 4 sisi", 'price' => 135000, 'stock' => 100, 'weight' => 420, 'healthy_score' => 68],
                    ['name' => 'Celana Kulot Wanita', 'description' => "Celana kulot lebar yang flowy untuk tampilan feminin dan tetap nyaman.\n\nSesuai untuk workwear maupun santai.", 'nutrition' => "Komposisi Bahan\n\n100% katun premium\nPotongan high waist\nSiluet palazo", 'price' => 179000, 'stock' => 0, 'weight' => 480, 'healthy_score' => 67],
                    ['name' => 'Celana Training', 'description' => "Celana training berjaring untuk olahraga dengan potongan santai dan elastis.\n\nKeringat mudah menyerap serta ringan dibawa ke gym.", 'nutrition' => "Komposisi Bahan\n\n100% poliester mesh\nElastis pinggang + tali serut\nDry-fit quick dry", 'price' => 149000, 'stock' => 88, 'weight' => 300, 'healthy_score' => 66, 'status' => 0],
                ],
            ],
        ];

        foreach ($categories as $category) {
            $products = $category['products'];
            unset($category['products']);

            $categoryModel = Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                $category
            );

            foreach ($products as $product) {
                Product::updateOrCreate(
                    ['slug' => Str::slug($product['name'])],
                    array_merge($product, [
                        'category_id' => $categoryModel->id,
                        'image' => null,
                        'status' => $product['status'] ?? 1,
                    ])
                );
            }
        }
    }
}