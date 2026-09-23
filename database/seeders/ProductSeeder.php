<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Tags\Tag;

class ProductSeeder extends Seeder
{
    /**
     * Vendedores fictícios usados para distribuir os produtos mockados.
     *
     * @var array<int, string>
     */
    private array $sellerNames = [
        'Ana Beatriz Souza',
        'Carlos Eduardo Lima',
        'Fernanda Oliveira',
        'João Pedro Almeida',
        'Mariana Costa',
        'Rafael Santos',
        'Juliana Pereira',
        'Lucas Ferreira',
        'Camila Rodrigues',
        'Bruno Carvalho',
        'Patrícia Gomes',
        'Thiago Martins',
    ];

    public function run(): void
    {
        $sellers = collect($this->sellerNames)->map(function (string $name, int $index) {
            return User::firstOrCreate(
                ['email' => 'vendedor'.($index + 1).'@marketplace.mock'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        });

        foreach ($this->catalog() as $tagName => $items) {
            Tag::findOrCreate($tagName);

            foreach ($items as $item) {
                $product = Product::where('name', $item['name'])->first();

                if ($product) {
                    // Reparo idempotente: se o produto já existe mas o arquivo de
                    // imagem sumiu (ex.: volume de storage recriado), baixa de novo
                    // em vez de deixar o card quebrado.
                    if (! $product->image || ! Storage::disk('public')->exists($product->image)) {
                        $product->update(['image' => $this->downloadImage($item['name'])]);
                    }

                    continue;
                }

                $product = Product::create([
                    'user_id' => $sellers->random()->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'image' => $this->downloadImage($item['name']),
                ]);

                $product->syncTags([$tagName]);
            }
        }
    }

    /**
     * Baixa uma imagem de exemplo e a armazena no disco público, retornando o caminho salvo.
     */
    private function downloadImage(string $seed): string
    {
        $filename = 'products/'.Str::slug($seed).'-'.Str::random(6).'.jpg';
        $url = 'https://picsum.photos/seed/'.urlencode(Str::slug($seed)).'/900/900';

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::timeout(20)->get($url);

                if ($response->successful()) {
                    Storage::disk('public')->put($filename, $response->body());

                    return $filename;
                }
            } catch (\Throwable $e) {
                // tenta novamente
            }
        }

        $fallback = 'products/placeholder.jpg';

        if (! Storage::disk('public')->exists($fallback)) {
            Storage::disk('public')->put($fallback, file_get_contents(public_path('imgs/logo-verde.png')));
        }

        return $fallback;
    }

    /**
     * Catálogo de produtos mockados agrupados por tag.
     *
     * @return array<string, array<int, array{name: string, description: string, price: int, stock: int}>>
     */
    private function catalog(): array
    {
        return [
            'Livros' => [
                ['name' => 'Harry Potter e a Pedra Filosofal', 'description' => 'Primeiro livro da saga escrita por J.K. Rowling, capa comum, edição nacional em português. Ideal para quem está começando a conhecer o mundo bruxo de Hogwarts.', 'price' => 3990, 'stock' => 25],
                ['name' => 'O Senhor dos Anéis - A Sociedade do Anel', 'description' => 'Volume inicial da trilogia de J.R.R. Tolkien, com mapa da Terra-média incluso. Uma jornada épica de fantasia clássica, em capa brochura.', 'price' => 5490, 'stock' => 18],
                ['name' => 'Dom Casmurro', 'description' => 'Clássico de Machado de Assis em edição de bolso, com posfácio e notas explicativas. Leitura obrigatória do vestibular e do Enem.', 'price' => 2490, 'stock' => 40],
                ['name' => '1984', 'description' => 'Distopia de George Orwell sobre vigilância e totalitarismo, tradução revisada e capa em brochura. Um dos romances mais influentes do século XX.', 'price' => 3490, 'stock' => 30],
                ['name' => 'O Pequeno Príncipe', 'description' => 'Edição ilustrada com as aquarelas originais de Antoine de Saint-Exupéry. Um presente atemporal para leitores de todas as idades.', 'price' => 2990, 'stock' => 35],
                ['name' => 'A Revolução dos Bichos', 'description' => 'Fábula política de George Orwell sobre uma fazenda que se rebela contra seus donos. Edição compacta, ótima para a primeira leitura do autor.', 'price' => 2790, 'stock' => 28],
                ['name' => 'Sapiens - Uma Breve História da Humanidade', 'description' => 'Best-seller de Yuval Noah Harari sobre a evolução da espécie humana, capa comum e mais de 400 páginas. Leitura envolvente de não ficção.', 'price' => 5990, 'stock' => 20],
                ['name' => 'Como Fazer Amigos e Influenciar Pessoas', 'description' => 'Clássico de Dale Carnegie sobre relações interpessoais e comunicação. Edição atualizada, muito usada em cursos de liderança.', 'price' => 3290, 'stock' => 22],
                ['name' => 'O Hobbit', 'description' => 'Aventura de Bilbo Bolseiro escrita por J.R.R. Tolkien, edição com ilustrações internas. Perfeito para conhecer o universo antes de O Senhor dos Anéis.', 'price' => 4490, 'stock' => 16],
                ['name' => 'Percy Jackson e o Ladrão de Raios', 'description' => 'Primeiro livro da série de Rick Riordan que mistura mitologia grega com aventura moderna. Sucesso entre leitores jovens e adultos.', 'price' => 3690, 'stock' => 24],
                ['name' => 'A Menina que Roubava Livros', 'description' => 'Romance de Markus Zusak ambientado na Alemanha nazista, narrado pela Morte. Edição de capa comum com mais de 500 páginas.', 'price' => 4790, 'stock' => 15],
                ['name' => 'Extraordinário', 'description' => 'História de superação de Auggie Pullman escrita por R.J. Palacio, sucesso entre jovens leitores e também adaptada para o cinema.', 'price' => 3390, 'stock' => 27],
                ['name' => 'A Culpa é das Estrelas', 'description' => 'Romance de John Green sobre dois jovens que se conhecem em um grupo de apoio a pacientes com câncer. Uma das obras mais vendidas do autor.', 'price' => 3190, 'stock' => 21],
                ['name' => 'Cem Anos de Solidão', 'description' => 'Obra-prima de Gabriel García Márquez e um dos maiores exemplos do realismo mágico latino-americano. Edição comemorativa em capa dura.', 'price' => 6490, 'stock' => 12],
                ['name' => 'O Alquimista', 'description' => 'Romance de Paulo Coelho traduzido para mais de 80 idiomas, sobre a jornada de um pastor em busca de seu tesouro pessoal.', 'price' => 2990, 'stock' => 33],
                ['name' => 'Box Trilogia Jogos Vorazes', 'description' => 'Coleção completa com os três livros da saga de Suzanne Collins em capa comum, dentro de uma caixa temática. Ótimo para presentear.', 'price' => 8990, 'stock' => 10],
                ['name' => 'Mindset - A Nova Psicologia do Sucesso', 'description' => 'Livro de Carol Dweck sobre como a mentalidade de crescimento pode transformar resultados pessoais e profissionais.', 'price' => 4290, 'stock' => 19],
                ['name' => 'Pai Rico, Pai Pobre', 'description' => 'Best-seller de Robert Kiyosaki sobre educação financeira, um dos livros mais recomendados para quem quer aprender a investir.', 'price' => 3790, 'stock' => 26],
                ['name' => 'Duna', 'description' => 'Clássico de ficção científica de Frank Herbert, edição com capa do filme e mapa do planeta Arrakis. Leitura essencial do gênero.', 'price' => 5290, 'stock' => 14],
                ['name' => 'Diário de um Banana', 'description' => 'Primeiro volume da série infanto-juvenil de Jeff Kinney, com ilustrações no estilo diário. Sucesso entre crianças e pré-adolescentes.', 'price' => 2690, 'stock' => 38],
            ],
            'Casa' => [
                ['name' => 'Jogo de Panelas Antiaderente Tramontina 5 Peças', 'description' => 'Conjunto com frigideira, caçarolas e molho, revestimento antiaderente e cabos em baquelite. Compatível com todos os tipos de fogão.', 'price' => 24990, 'stock' => 12],
                ['name' => 'Aspirador de Pó Vertical Electrolux', 'description' => 'Aspirador 2 em 1 com filtro HEPA e reservatório de fácil limpeza, ideal para pisos e estofados.', 'price' => 34990, 'stock' => 10],
                ['name' => 'Conjunto de Toalhas de Banho 100% Algodão', 'description' => 'Kit com 4 toalhas felpudas de alta absorção, disponíveis em diversas cores para combinar com o banheiro.', 'price' => 8990, 'stock' => 22],
                ['name' => 'Air Fryer Mondial 4L', 'description' => 'Fritadeira elétrica sem óleo com painel digital e 8 funções pré-programadas, prepara refeições mais saudáveis em minutos.', 'price' => 29990, 'stock' => 18],
                ['name' => 'Liquidificador Philips Walita 1200W', 'description' => 'Liquidificador de alta potência com copo em tritan resistente a impactos e lâminas em inox reforçado.', 'price' => 26990, 'stock' => 14],
                ['name' => 'Jogo de Cama Queen 200 Fios', 'description' => 'Lençol com elástico, fronhas e sobrecama em algodão percal, toque macio e alta durabilidade.', 'price' => 15990, 'stock' => 20],
                ['name' => 'Kit Organizadores de Armário Multiuso', 'description' => 'Conjunto com 10 caixas organizadoras empilháveis, ideais para roupas, acessórios e itens de cozinha.', 'price' => 7990, 'stock' => 30],
                ['name' => 'Panela de Pressão Elétrica Digital', 'description' => 'Panela multifuncional com 10 programas automáticos, cozinha arroz, feijão, carnes e sopas com segurança.', 'price' => 32990, 'stock' => 11],
                ['name' => 'Luminária de Mesa LED Articulada', 'description' => 'Luminária com braço flexível e três níveis de intensidade de luz, ideal para leitura e home office.', 'price' => 8990, 'stock' => 25],
                ['name' => 'Cafeteira Elétrica Programável Mondial', 'description' => 'Cafeteira com timer programável e jarra térmica que mantém o café quente por mais tempo.', 'price' => 17990, 'stock' => 16],
                ['name' => 'Tapete Sala de Estar 2x1,5m', 'description' => 'Tapete macio antiderrapante, fácil de limpar e disponível em tons neutros para decoração.', 'price' => 19990, 'stock' => 13],
                ['name' => 'Conjunto de Talheres Inox 24 Peças', 'description' => 'Kit completo para 6 pessoas em aço inoxidável polido, acompanha estojo para guardar.', 'price' => 12990, 'stock' => 24],
                ['name' => 'Ferro de Passar a Vapor Philco', 'description' => 'Ferro com sistema de vapor contínuo e base em cerâmica que desliza suavemente sobre os tecidos.', 'price' => 13990, 'stock' => 19],
                ['name' => 'Cortina Blackout Corta Luz 2 Metros', 'description' => 'Cortina com tecido duplo que bloqueia até 90% da luz externa, ótima para quartos e home theater.', 'price' => 11990, 'stock' => 21],
                ['name' => 'Espremedor de Frutas Elétrico', 'description' => 'Espremedor com sistema de cone reversível, ideal para preparar sucos naturais rapidamente.', 'price' => 9990, 'stock' => 17],
                ['name' => 'Kit Potes Herméticos de Cozinha 10 Peças', 'description' => 'Potes empilháveis com tampa hermética que mantêm os alimentos frescos por mais tempo.', 'price' => 8490, 'stock' => 28],
                ['name' => 'Ventilador de Coluna Mondial 40cm', 'description' => 'Ventilador com 3 velocidades e oscilação automática, ótimo para refrescar ambientes grandes.', 'price' => 15990, 'stock' => 15],
                ['name' => 'Escada Doméstica Multiuso 3 Degraus', 'description' => 'Escada dobrável em alumínio, leve e resistente, com degraus antiderrapantes para maior segurança.', 'price' => 12990, 'stock' => 12],
                ['name' => 'Edredom Casal Microfibra', 'description' => 'Edredom dupla face macio e leve, perfeito para os dias mais frios sem pesar na cama.', 'price' => 17990, 'stock' => 20],
                ['name' => 'Grill Elétrico George Foreman', 'description' => 'Grelha elétrica antiaderente com sistema de inclinação que escorre o excesso de gordura das carnes.', 'price' => 21990, 'stock' => 14],
            ],
            'Eletrônicos' => [
                ['name' => 'Smartphone Samsung Galaxy A55 256GB', 'description' => 'Tela Super AMOLED de 6,6", câmera tripla de 50MP e bateria de 5000mAh com carregamento rápido.', 'price' => 249900, 'stock' => 15],
                ['name' => 'Smart TV LG 50" 4K UHD', 'description' => 'Resolução 4K com processador de imagem por IA, sistema webOS e suporte à Alexa e Google Assistente.', 'price' => 219900, 'stock' => 8],
                ['name' => 'Fone de Ouvido Bluetooth JBL Tune 510BT', 'description' => 'Fone on-ear com até 40 horas de bateria e som JBL Pure Bass, dobrável para transporte fácil.', 'price' => 24990, 'stock' => 30],
                ['name' => 'Caixa de Som Bluetooth JBL Charge 5', 'description' => "Caixa de som portátil à prova d'água com até 20 horas de bateria e função powerbank.", 'price' => 89900, 'stock' => 12],
                ['name' => 'Smartwatch Xiaomi Mi Band 8', 'description' => 'Pulseira inteligente com tela AMOLED, monitor de saúde 24h e mais de 150 modos esportivos.', 'price' => 19990, 'stock' => 40],
                ['name' => 'Câmera de Segurança Wi-Fi Intelbras', 'description' => 'Câmera com visão noturna, detecção de movimento e acesso remoto pelo aplicativo.', 'price' => 17990, 'stock' => 22],
                ['name' => 'Carregador Portátil Power Bank 20000mAh', 'description' => 'Powerbank com duas saídas USB e carregamento rápido, ideal para viagens e uso diário.', 'price' => 12990, 'stock' => 35],
                ['name' => 'Fone de Ouvido TWS Sem Fio', 'description' => 'Fone intra-auricular sem fio com estojo de carregamento e cancelamento de ruído passivo.', 'price' => 14990, 'stock' => 45],
                ['name' => 'Videogame Portátil Retrô', 'description' => 'Console portátil com mais de 500 jogos clássicos pré-instalados e tela de 3 polegadas.', 'price' => 15990, 'stock' => 20],
                ['name' => 'Roteador Wi-Fi 6 TP-Link', 'description' => 'Roteador de alta performance com tecnologia Wi-Fi 6, ideal para casas com muitos dispositivos conectados.', 'price' => 34990, 'stock' => 10],
                ['name' => 'Tablet Samsung Galaxy Tab A9', 'description' => 'Tablet com tela de 8,7", processador octa-core e até 13 horas de bateria para entretenimento e estudo.', 'price' => 99900, 'stock' => 14],
                ['name' => 'Drone com Câmera 4K', 'description' => 'Drone dobrável com câmera 4K estabilizada, retorno automático e até 25 minutos de voo.', 'price' => 59900, 'stock' => 9],
                ['name' => 'Impressora Multifuncional HP Deskjet', 'description' => 'Imprime, copia e digitaliza com conexão Wi-Fi, compatível com impressão via smartphone.', 'price' => 44990, 'stock' => 11],
                ['name' => 'Kindle Paperwhite 16GB', 'description' => 'E-reader à prova d\'água com tela de 6,8" sem reflexo e luz ajustável para leitura noturna.', 'price' => 59900, 'stock' => 13],
                ['name' => 'Barra de Som Soundbar Samsung', 'description' => 'Soundbar 2.1 canais com subwoofer sem fio e conexão Bluetooth para TV e outros dispositivos.', 'price' => 79900, 'stock' => 8],
                ['name' => 'Micro-ondas Digital 20L', 'description' => 'Micro-ondas com painel digital, grill e diversas funções pré-programadas para o dia a dia.', 'price' => 54990, 'stock' => 12],
                ['name' => 'Chromecast Google TV 4K', 'description' => 'Transforme qualquer TV em smart TV com streaming em 4K HDR e controle por voz.', 'price' => 39900, 'stock' => 25],
                ['name' => 'Câmera Instantânea Instax Mini', 'description' => 'Câmera compacta que revela fotos na hora, com flash automático e visor óptico.', 'price' => 34990, 'stock' => 16],
                ['name' => 'Ar Condicionado Portátil 9000 BTUs', 'description' => 'Climatizador portátil com controle remoto e função desumidificador, não precisa de instalação fixa.', 'price' => 189900, 'stock' => 6],
                ['name' => 'Carregador Veicular Turbo USB-C', 'description' => 'Carregador para carro com duas portas USB-C e potência de 65W para carregamento ultrarrápido.', 'price' => 8990, 'stock' => 40],
            ],
            'Computadores' => [
                ['name' => 'Notebook Dell Inspiron 15 i5 8GB 256GB SSD', 'description' => 'Notebook com processador Intel Core i5, tela de 15,6" Full HD e SSD para maior velocidade no dia a dia.', 'price' => 349900, 'stock' => 9],
                ['name' => 'Mouse Gamer Logitech G203', 'description' => 'Mouse com sensor óptico de alta precisão e iluminação RGB personalizável, ideal para jogos competitivos.', 'price' => 12990, 'stock' => 35],
                ['name' => 'Teclado Mecânico RGB Redragon', 'description' => 'Teclado mecânico com switches táteis, iluminação RGB e construção reforçada para uso gamer.', 'price' => 24990, 'stock' => 20],
                ['name' => 'Monitor LED 24" Full HD LG', 'description' => 'Monitor com painel IPS, taxa de atualização de 75Hz e bordas finas para maior imersão.', 'price' => 74900, 'stock' => 12],
                ['name' => 'SSD NVMe Kingston 1TB', 'description' => 'Unidade de armazenamento NVMe com velocidades de leitura de até 3500MB/s, reduz drasticamente o tempo de boot.', 'price' => 39990, 'stock' => 18],
                ['name' => 'Placa de Vídeo RTX 4060 8GB', 'description' => 'GPU com suporte a ray tracing e DLSS 3, ideal para jogos em Full HD e QHD com alto desempenho.', 'price' => 219900, 'stock' => 6],
                ['name' => 'Webcam Full HD Logitech C920', 'description' => 'Webcam com gravação em 1080p e microfone estéreo integrado, ótima para reuniões e streaming.', 'price' => 34990, 'stock' => 16],
                ['name' => 'Headset Gamer HyperX Cloud Stinger', 'description' => 'Headset leve com almofadas confortáveis e microfone com cancelamento de ruído giratório.', 'price' => 29990, 'stock' => 22],
                ['name' => 'Cadeira Gamer Ergonômica', 'description' => 'Cadeira com apoio lombar ajustável, braços 3D e reclinação de até 180 graus.', 'price' => 89900, 'stock' => 8],
                ['name' => 'Processador AMD Ryzen 5 5600', 'description' => 'Processador de 6 núcleos e 12 threads, excelente custo-benefício para jogos e produtividade.', 'price' => 79900, 'stock' => 10],
                ['name' => 'Memória RAM 16GB DDR4 Corsair', 'description' => 'Kit de memória com dissipador de calor e alta frequência para melhor desempenho em multitarefas.', 'price' => 34990, 'stock' => 20],
                ['name' => 'Mousepad Gamer Extra Grande', 'description' => 'Mousepad com base emborrachada antiderrapante e superfície de tecido para deslize suave.', 'price' => 6990, 'stock' => 40],
                ['name' => 'HD Externo Seagate 2TB', 'description' => 'Armazenamento externo portátil compatível com PC, notebook e consoles, conexão USB 3.0.', 'price' => 44990, 'stock' => 14],
                ['name' => 'Roteador Mesh Wi-Fi para Casa', 'description' => 'Sistema mesh com dois pontos de acesso que eliminam áreas sem sinal em casas grandes.', 'price' => 59900, 'stock' => 7],
                ['name' => 'Impressora 3D Creality Ender 3', 'description' => 'Impressora 3D de código aberto, ideal para iniciantes em prototipagem e projetos criativos.', 'price' => 129900, 'stock' => 5],
                ['name' => 'Fonte de Alimentação 650W 80 Plus', 'description' => 'Fonte certificada com proteção contra picos de energia, indicada para PCs gamers de médio porte.', 'price' => 44990, 'stock' => 13],
                ['name' => 'Gabinete Gamer com Fans RGB', 'description' => 'Gabinete em vidro temperado com três coolers RGB inclusos e boa circulação de ar.', 'price' => 29990, 'stock' => 15],
                ['name' => 'Notebook Gamer Acer Nitro 5', 'description' => 'Notebook com placa de vídeo dedicada, processador Ryzen 5 e tela de 144Hz para jogos fluidos.', 'price' => 449900, 'stock' => 6],
                ['name' => 'Hub USB-C 7 em 1', 'description' => 'Adaptador com saídas HDMI, USB 3.0 e leitor de cartão, expande as conexões do notebook.', 'price' => 15990, 'stock' => 28],
                ['name' => 'Estabilizador de Energia 1000VA', 'description' => 'Protege computadores e periféricos contra variações de tensão e quedas de energia.', 'price' => 24990, 'stock' => 17],
            ],
            'Games' => [
                ['name' => 'PlayStation 5 Slim 1TB', 'description' => 'Console de nova geração com SSD ultrarrápido, suporte a 4K e o controle DualSense com feedback háptico.', 'price' => 399900, 'stock' => 7],
                ['name' => 'Xbox Series S 512GB', 'description' => 'Console compacto totalmente digital, com carregamento rápido e jogos em até 1440p.', 'price' => 259900, 'stock' => 9],
                ['name' => 'Nintendo Switch OLED', 'description' => 'Console híbrido com tela OLED de 7 polegadas, ideal para jogar em casa ou no modo portátil.', 'price' => 249900, 'stock' => 8],
                ['name' => 'Controle DualSense PS5', 'description' => 'Controle sem fio com feedback háptico e gatilhos adaptáveis, compatível com PS5.', 'price' => 44990, 'stock' => 20],
                ['name' => 'Jogo EA Sports FC 25 - PS5', 'description' => 'Simulador de futebol com licenças oficiais de times e jogadores, modo carreira e Ultimate Team.', 'price' => 24990, 'stock' => 25],
                ['name' => 'Jogo The Legend of Zelda: Tears of the Kingdom', 'description' => 'Aventura de mundo aberto para Nintendo Switch, sequência aclamada de Breath of the Wild.', 'price' => 29990, 'stock' => 14],
                ['name' => 'Headset Gamer Sem Fio para PS5', 'description' => 'Headset sem fio com áudio 3D e até 12 horas de bateria, compatível com PS5 e PC.', 'price' => 49990, 'stock' => 12],
                ['name' => 'Cadeira Gamer com Apoio de Braço 4D', 'description' => 'Cadeira reclinável com almofadas de pescoço e lombar, estrutura reforçada para longas sessões de jogo.', 'price' => 99900, 'stock' => 6],
                ['name' => 'Volante com Pedais para Corrida', 'description' => 'Volante com force feedback e pedais de aceleração e freio, compatível com PC e consoles.', 'price' => 69900, 'stock' => 5],
                ['name' => 'Jogo God of War Ragnarök - PS5', 'description' => 'Continuação da saga nórdica de Kratos e Atreus, um dos jogos mais aclamados da geração.', 'price' => 29990, 'stock' => 15],
                ['name' => 'Cartão PSN R$100', 'description' => 'Cartão pré-pago para adicionar saldo na PlayStation Store e comprar jogos e assinaturas.', 'price' => 10000, 'stock' => 50],
                ['name' => 'Jogo Mario Kart 8 Deluxe - Switch', 'description' => 'Clássico jogo de corrida com todos os personagens e pistas do Mario Kart 8 e mais conteúdo exclusivo.', 'price' => 24990, 'stock' => 20],
                ['name' => 'Base de Carregamento Dupla DualSense', 'description' => 'Carrega dois controles DualSense simultaneamente sem precisar conectar ao console.', 'price' => 17990, 'stock' => 18],
                ['name' => 'Óculos de Realidade Virtual Meta Quest 3', 'description' => 'Óculos de VR standalone com passthrough colorido e biblioteca de jogos imersivos.', 'price' => 249900, 'stock' => 5],
                ['name' => 'Teclado e Mouse Gamer Combo', 'description' => 'Kit com teclado semi-mecânico e mouse com iluminação RGB, ideal para jogos no PC.', 'price' => 19990, 'stock' => 22],
                ['name' => 'Jogo Minecraft - Nintendo Switch', 'description' => 'Jogo de construção e sobrevivência em mundo aberto, sucesso entre jogadores de todas as idades.', 'price' => 19990, 'stock' => 30],
                ['name' => 'Capa Protetora para Nintendo Switch', 'description' => 'Case rígido com compartimentos para até 10 cartuchos de jogos, protege o console em viagens.', 'price' => 8990, 'stock' => 35],
                ['name' => 'Jogo Call of Duty: Black Ops 6 - Xbox', 'description' => 'Novo capítulo da franquia de FPS com campanha, multiplayer e modo zumbi.', 'price' => 34990, 'stock' => 12],
                ['name' => 'Mesa para Cadeira Gamer com Suporte', 'description' => 'Mesa ajustável em altura com suporte para monitor e organização de cabos.', 'price' => 59900, 'stock' => 9],
                ['name' => 'Assinatura Xbox Game Pass Ultimate 3 Meses', 'description' => 'Acesso a mais de 100 jogos no console, PC e nuvem, incluindo lançamentos no dia do lançamento.', 'price' => 14990, 'stock' => 60],
            ],
            'Brinquedos' => [
                ['name' => 'Lego Classic Caixa de Peças Criativas', 'description' => 'Conjunto com 484 peças coloridas para estimular a criatividade e montar construções livres.', 'price' => 24990, 'stock' => 20],
                ['name' => 'Boneca Barbie Fashionista', 'description' => 'Boneca articulada com roupas e acessórios da moda, parte da linha inclusiva Fashionista.', 'price' => 8990, 'stock' => 30],
                ['name' => 'Carrinho Hot Wheels Kit 5 Unidades', 'description' => 'Pacote com 5 miniaturas de carros em escala, ideal para colecionar e brincar.', 'price' => 6990, 'stock' => 40],
                ['name' => 'Quebra-Cabeça 1000 Peças Paisagem', 'description' => 'Quebra-cabeça com imagem de paisagem natural, ótimo passatempo para toda a família.', 'price' => 4990, 'stock' => 25],
                ['name' => 'Pelúcia Urso Gigante 80cm', 'description' => 'Urso de pelúcia macio e fofo, ótimo presente para bebês e crianças pequenas.', 'price' => 12990, 'stock' => 15],
                ['name' => 'Jogo de Tabuleiro Banco Imobiliário', 'description' => 'Clássico jogo de compra e venda de propriedades para reunir a família e os amigos.', 'price' => 8990, 'stock' => 22],
                ['name' => 'Boneco Action Figure Homem-Aranha', 'description' => 'Figura articulada de 30cm com detalhes fiéis ao personagem dos quadrinhos e filmes.', 'price' => 9990, 'stock' => 18],
                ['name' => 'Slime Kit Completo Para Crianças', 'description' => 'Kit com ingredientes e acessórios para criar diferentes texturas e cores de slime em casa.', 'price' => 5990, 'stock' => 35],
                ['name' => 'Pista de Carrinhos Hot Wheels com Loop', 'description' => 'Pista de montar com loop e rampas, acompanha um carrinho para começar a diversão.', 'price' => 14990, 'stock' => 16],
                ['name' => 'Kit Massinha de Modelar 12 Cores', 'description' => 'Potes de massinha atóxica em 12 cores vibrantes, com moldes e ferramentas inclusos.', 'price' => 3990, 'stock' => 45],
                ['name' => 'Boneca Bebê Reborn Realista', 'description' => 'Boneca com aparência realista de bebê, feita em vinil siliconado, acompanha manta e mamadeira.', 'price' => 18990, 'stock' => 10],
                ['name' => 'Drone de Brinquedo para Iniciantes', 'description' => 'Mini drone fácil de pilotar, com proteção de hélices e controle remoto intuitivo.', 'price' => 12990, 'stock' => 20],
                ['name' => 'Blocos de Montar Compatível 500 Peças', 'description' => 'Conjunto de blocos de montar compatíveis com as principais marcas do mercado.', 'price' => 9990, 'stock' => 28],
                ['name' => 'Patinete Infantil 3 Rodas', 'description' => 'Patinete com estrutura de alumínio leve e guidão ajustável em altura.', 'price' => 19990, 'stock' => 14],
                ['name' => 'Jogo Uno Cartas Clássico', 'description' => 'Baralho oficial do jogo Uno, ideal para diversão em família com regras simples.', 'price' => 2990, 'stock' => 50],
                ['name' => 'Cozinha Infantil de Brinquedo com Acessórios', 'description' => 'Cozinha em miniatura com fogão, pia e utensílios para estimular a brincadeira de faz de conta.', 'price' => 24990, 'stock' => 12],
                ['name' => 'Kit Dinossauros de Brinquedo 10 Peças', 'description' => 'Conjunto com 10 miniaturas de dinossauros variados, ótimo para brincadeiras educativas.', 'price' => 5990, 'stock' => 30],
                ['name' => 'Boneco Colecionável Estilo Pop', 'description' => 'Figura colecionável em vinil no estilo Pop, com caixa ilustrada original.', 'price' => 8990, 'stock' => 25],
                ['name' => 'Piscina de Bolinhas Infantil', 'description' => 'Piscina inflável que acompanha bolinhas coloridas, ideal para diversão dentro de casa.', 'price' => 17990, 'stock' => 10],
                ['name' => 'Triciclo Infantil com Empurrador', 'description' => 'Triciclo com haste removível para os pais empurrarem e cinto de segurança para o bebê.', 'price' => 22990, 'stock' => 11],
            ],
            'Beleza' => [
                ['name' => 'Perfume Feminino Eau de Parfum 100ml', 'description' => 'Fragrância floral amadeirada de longa fixação, frasco de 100ml com embalagem para presente.', 'price' => 18990, 'stock' => 20],
                ['name' => 'Kit Maquiagem Profissional Completo', 'description' => 'Kit com paleta de sombras, base, batons e pincéis, ideal para uso profissional ou pessoal.', 'price' => 14990, 'stock' => 15],
                ['name' => 'Chapinha Modeladora de Cabelo Titanium', 'description' => 'Placas de titânio que aquecem rapidamente e deslizam suavemente pelos fios, alisando sem danificar.', 'price' => 12990, 'stock' => 18],
                ['name' => 'Secador de Cabelo Profissional 2200W', 'description' => 'Secador com motor potente e íons negativos que reduzem o frizz e aceleram a secagem.', 'price' => 15990, 'stock' => 20],
                ['name' => 'Kit Shampoo e Condicionador Reconstrução', 'description' => 'Kit com fórmula reparadora para cabelos danificados, com queratina e óleo de argan.', 'price' => 8990, 'stock' => 25],
                ['name' => 'Paleta de Sombras 24 Cores', 'description' => 'Paleta com tons neutros e vibrantes, alta pigmentação e boa durabilidade.', 'price' => 6990, 'stock' => 30],
                ['name' => 'Base Líquida de Alta Cobertura', 'description' => 'Base com acabamento matte e alta cobertura, disponível em diversos tons de pele.', 'price' => 5990, 'stock' => 35],
                ['name' => 'Batom Matte Longa Duração', 'description' => 'Batom com fórmula matte que não resseca os lábios e dura o dia todo.', 'price' => 3490, 'stock' => 40],
                ['name' => 'Creme Hidratante Facial Anti-idade', 'description' => 'Creme com ácido hialurônico e vitamina C, reduz linhas finas e hidrata profundamente.', 'price' => 8990, 'stock' => 22],
                ['name' => 'Escova Alisadora Elétrica', 'description' => 'Escova que alisa e dá brilho aos fios em uma única passada, sem precisar de chapinha tradicional.', 'price' => 13990, 'stock' => 16],
                ['name' => 'Kit Pincéis de Maquiagem 12 Peças', 'description' => 'Conjunto de pincéis macios com cerdas sintéticas, acompanha estojo para transporte.', 'price' => 6990, 'stock' => 28],
                ['name' => 'Máscara de Cílios Volume Extremo', 'description' => 'Máscara com fórmula que alonga e dá volume aos cílios sem formar grumos.', 'price' => 3990, 'stock' => 38],
                ['name' => 'Protetor Solar Facial FPS 60', 'description' => 'Proteção UVA/UVB com toque seco e efeito matificante, indicado para uso diário.', 'price' => 6490, 'stock' => 30],
                ['name' => 'Necessaire de Maquiagem Organizadora', 'description' => 'Necessaire espaçosa com compartimentos internos para organizar maquiagens e pincéis.', 'price' => 4990, 'stock' => 25],
                ['name' => 'Óleo Capilar Reparador de Pontas', 'description' => 'Óleo leve à base de argan que nutre e reduz o frizz sem pesar nos fios.', 'price' => 5490, 'stock' => 27],
                ['name' => 'Espelho de Maquiagem com Led', 'description' => 'Espelho com iluminação LED regulável e ampliação, ideal para maquiagem de precisão.', 'price' => 9990, 'stock' => 14],
                ['name' => 'Kit Manicure e Pedicure Elétrico', 'description' => 'Kit com múltiplas pontas para lixar, polir e cuidar das unhas das mãos e dos pés.', 'price' => 11990, 'stock' => 18],
                ['name' => 'Perfume Masculino Eau de Toilette 100ml', 'description' => 'Fragrância amadeirada com notas cítricas, frasco de 100ml e boa fixação durante o dia.', 'price' => 17990, 'stock' => 20],
                ['name' => 'Máquina de Corte de Cabelo Profissional', 'description' => 'Máquina com lâminas de aço inoxidável e várias pentes-guia para diferentes comprimentos.', 'price' => 12990, 'stock' => 15],
                ['name' => 'Argila Facial Purificante', 'description' => 'Máscara de argila que remove impurezas e controla a oleosidade da pele.', 'price' => 4490, 'stock' => 32],
            ],
            'Alimentos' => [
                ['name' => 'Café em Grãos Torrado 1kg', 'description' => 'Grãos 100% arábica torrados em ponto médio, ideal para preparo em cafeteira ou prensa francesa.', 'price' => 4990, 'stock' => 30],
                ['name' => 'Barra de Chocolate Belga 500g', 'description' => 'Chocolate ao leite belga com alto teor de cacau, ideal para sobremesas e consumo direto.', 'price' => 3990, 'stock' => 25],
                ['name' => 'Azeite de Oliva Extra Virgem 500ml', 'description' => 'Azeite prensado a frio, acidez baixa e sabor equilibrado para saladas e finalização de pratos.', 'price' => 5990, 'stock' => 22],
                ['name' => 'Mel Puro Silvestre 1kg', 'description' => 'Mel de flores silvestres sem adição de açúcar, extraído de forma artesanal.', 'price' => 4490, 'stock' => 20],
                ['name' => 'Whey Protein Concentrado 900g', 'description' => 'Suplemento proteico sabor chocolate, ideal para ganho de massa muscular e recuperação pós-treino.', 'price' => 12990, 'stock' => 18],
                ['name' => 'Kit Temperos Gourmet Importados', 'description' => 'Conjunto com 6 potes de especiarias selecionadas para realçar o sabor dos pratos do dia a dia.', 'price' => 7990, 'stock' => 24],
                ['name' => 'Vinho Tinto Seco Reserva 750ml', 'description' => 'Vinho encorpado com notas de frutas vermelhas, envelhecido em barril de carvalho.', 'price' => 8990, 'stock' => 15],
                ['name' => 'Granola Artesanal com Castanhas 1kg', 'description' => 'Granola crocante com mix de castanhas, aveia e mel, sem conservantes artificiais.', 'price' => 3490, 'stock' => 28],
                ['name' => 'Queijo Parmesão Ralado 200g', 'description' => 'Queijo parmesão ralado na hora, embalagem a vácuo que preserva o sabor por mais tempo.', 'price' => 2990, 'stock' => 26],
                ['name' => 'Cesta de Café da Manhã Completa', 'description' => 'Cesta com pães, geleias, café e frutas selecionadas, ideal para presentear em datas especiais.', 'price' => 14990, 'stock' => 10],
                ['name' => 'Amêndoas e Castanhas Mix 500g', 'description' => 'Mix de castanhas selecionadas sem sal, rico em fibras e gorduras boas.', 'price' => 6490, 'stock' => 22],
                ['name' => 'Chá Verde Orgânico 20 Sachês', 'description' => 'Chá verde 100% orgânico, rico em antioxidantes, ideal para o dia a dia.', 'price' => 1990, 'stock' => 35],
                ['name' => 'Geleia de Frutas Vermelhas Artesanal', 'description' => 'Geleia artesanal feita com frutas vermelhas selecionadas e baixo teor de açúcar.', 'price' => 2490, 'stock' => 24],
                ['name' => 'Macarrão Italiano Grano Duro 500g', 'description' => 'Massa italiana feita com trigo grano duro, textura firme mesmo após o cozimento.', 'price' => 1490, 'stock' => 40],
                ['name' => 'Água de Coco Integral 1L', 'description' => 'Água de coco natural sem adição de açúcar ou conservantes, refrescante e hidratante.', 'price' => 990, 'stock' => 50],
                ['name' => 'Biscoito Amanteigado Importado', 'description' => 'Biscoitos amanteigados em lata decorativa, perfeitos para acompanhar café ou chá.', 'price' => 3990, 'stock' => 20],
                ['name' => 'Suco Integral de Uva 1,5L', 'description' => 'Suco de uva integral sem adição de água ou açúcar, feito a partir de uvas selecionadas.', 'price' => 2290, 'stock' => 30],
                ['name' => 'Farinha de Amêndoas 500g', 'description' => 'Farinha sem glúten ideal para receitas fitness e low carb.', 'price' => 4990, 'stock' => 18],
                ['name' => 'Molho de Pimenta Artesanal', 'description' => 'Molho artesanal picante feito com pimentas selecionadas, ótimo para carnes e petiscos.', 'price' => 1990, 'stock' => 26],
                ['name' => 'Kit Especiarias para Churrasco', 'description' => 'Conjunto com temperos especiais para carnes na churrasqueira, incluindo sal grosso aromatizado.', 'price' => 5490, 'stock' => 22],
            ],
        ];
    }
}
