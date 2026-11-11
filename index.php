<?php

require_once __DIR__ . "/conexao.php";

// Informações das imagens e tamanhos de cada produto
$detalhes = [
    "Camiseta Básica" => [
        "imagem" => "camiseta-basica.jpg",
        "tamanhos" => ["P", "M", "G", "GG"]
    ],
    "Calça Jeans" => [
        "imagem" => "calca-jeans.jpg",
        "tamanhos" => ["38", "40", "42", "44", "46"]
    ],
    "Moletom Casual" => [
        "imagem" => "moletom-casual.jpg",
        "tamanhos" => ["P", "M", "G", "GG"]
    ],
    "Blusa Elegante" => [
        "imagem" => "blusa-elegante.jpg",
        "tamanhos" => ["P", "M", "G", "GG"]
    ],
    "Calça Feminina" => [
        "imagem" => "calca-feminina.jpg",
        "tamanhos" => ["36", "38", "40", "42", "44"]
    ],
    "Vestido Casual" => [
        "imagem" => "vestido-casual.jpg",
        "tamanhos" => ["P", "M", "G", "GG"]
    ]
];

// Busca os produtos no banco de dados
$sql = "SELECT * FROM produtos ORDER BY id_produto ASC";
$resultado = $conexao->query($sql);

if (!$resultado) {
    die("Erro ao buscar produtos: " . $conexao->error);
}

$masculinos = [];
$femininos = [];
$todosProdutos = [];

while ($linha = $resultado->fetch_assoc()) {

    $nome = $linha["nome"];

    $produto = [
        "id" => $linha["id_produto"],
        "nome" => $nome,
        "preco" => (float) $linha["preco"],
        "categoria" => ((int) $linha["id_categoria"] === 1)
            ? "Masculino"
            : "Feminino",
        "imagem" => $detalhes[$nome]["imagem"] ?? "",
        "descricao" => $linha["descricao"],
        "tamanhos" => $detalhes[$nome]["tamanhos"] ?? [],
        "estoque" => $linha["estoque"]
    ];

    $todosProdutos[] = $produto;

    if ((int) $linha["id_categoria"] === 1) {
        $masculinos[] = $produto;
    } elseif ((int) $linha["id_categoria"] === 2) {
        $femininos[] = $produto;
    }
}

$produtoSelecionado = null;

if (isset($_GET["produto"])) {
    foreach ($todosProdutos as $produto) {
        if ((string) $produto["id"] === (string) $_GET["produto"]) {
            $produtoSelecionado = $produto;
            break;
        }
    }
}

function mostrarPreco($preco) {
    return "R$ " . number_format($preco, 2, ",", ".");
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Urban Style | Moda Masculina e Feminina</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .imagem-produto {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
            border-radius: 4px;
        }

        .detalhes-produto {
            max-width: 1000px;
            margin: 60px auto;
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .detalhes-produto img {
            width: 100%;
            max-height: 550px;
            object-fit: cover;
            border-radius: 8px;
        }

        .informacoes-produto h1 {
            font-size: 34px;
            margin: 15px 0;
        }

        .informacoes-produto .preco {
            font-size: 26px;
            margin: 20px 0;
        }

        .tamanhos {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 15px 0 25px;
        }

        .tamanho {
            border: 1px solid #aaa;
            padding: 10px 16px;
            border-radius: 4px;
        }

        .voltar {
            display: inline-block;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .detalhes-produto {
                grid-template-columns: 1fr;
                padding: 20px;
                margin: 25px auto;
            }

            .informacoes-produto h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

<header class="topo">

    <div class="logo">URBAN <span>STYLE</span></div>

    <nav>
        <a href="index.php#inicio">Início</a>
        <a href="index.php#masculino">Masculino</a>
        <a href="index.php#feminino">Feminino</a>
        <a href="index.php#sobre">Sobre</a>
        <a href="index.php#contato">Contato</a>
    </nav>

</header>

<main>

<?php if ($produtoSelecionado !== null): ?>

    <section class="detalhes-produto" id="detalhes">

        <img
            src="img/<?= htmlspecialchars($produtoSelecionado["imagem"]) ?>"
            alt="<?= htmlspecialchars($produtoSelecionado["nome"]) ?>"
        >

        <div class="informacoes-produto">

            <p class="categoria">
                <?= htmlspecialchars($produtoSelecionado["categoria"]) ?>
            </p>

            <h1>
                <?= htmlspecialchars($produtoSelecionado["nome"]) ?>
            </h1>

            <p class="preco">
                <?= mostrarPreco($produtoSelecionado["preco"]) ?>
            </p>

            <p>
                <?= htmlspecialchars($produtoSelecionado["descricao"]) ?>
            </p>

            <h3>Escolha seu tamanho:</h3>

            <div class="tamanhos">

                <?php foreach ($produtoSelecionado["tamanhos"] as $tamanho): ?>

                    <span class="tamanho">
                        <?= htmlspecialchars($tamanho) ?>
                    </span>

                <?php endforeach; ?>

            </div>

            <p>
                Estoque disponível:
                <?= (int) $produtoSelecionado["estoque"] ?> unidades
            </p>

            <a
                class="botao"
                href="index.php#<?= strtolower($produtoSelecionado["categoria"]) ?>"
            >
                Voltar à coleção
            </a>

        </div>

    </section>

<?php else: ?>

    <section class="banner" id="inicio">

        <div class="banner-conteudo">

            <p class="subtitulo">NOVA COLEÇÃO</p>

            <h1>Seu estilo.<br>Sua identidade.</h1>

            <p>Moda masculina e feminina para todos os momentos.</p>

            <a href="#masculino" class="botao">
                Conheça a coleção
            </a>

        </div>

    </section>

    <section class="colecao container" id="masculino">

        <div class="titulo-secao">
            <p>EXPLORE NOSSA LOJA</p>
            <h2>Moda Masculina</h2>
        </div>

        <div class="produtos">

            <?php foreach ($masculinos as $produto): ?>

                <article class="produto">

                    <img
                        class="imagem-produto"
                        src="img/<?= htmlspecialchars($produto["imagem"]) ?>"
                        alt="<?= htmlspecialchars($produto["nome"]) ?>"
                    >

                    <p class="categoria">
                        <?= htmlspecialchars($produto["categoria"]) ?>
                    </p>

                    <h3>
                        <?= htmlspecialchars($produto["nome"]) ?>
                    </h3>

                    <p class="preco">
                        <?= mostrarPreco($produto["preco"]) ?>
                    </p>

                    <a
                        class="botao"
                        href="?produto=<?= urlencode($produto["id"]) ?>#detalhes"
                    >
                        Ver produto
                    </a>

                </article>

            <?php endforeach; ?>

        </div>

    </section>

    <section class="colecao container" id="feminino">

        <div class="titulo-secao">
            <p>ENCONTRE SEU ESTILO</p>
            <h2>Moda Feminina</h2>
        </div>

        <div class="produtos">

            <?php foreach ($femininos as $produto): ?>

                <article class="produto">

                    <img
                        class="imagem-produto"
                        src="img/<?= htmlspecialchars($produto["imagem"]) ?>"
                        alt="<?= htmlspecialchars($produto["nome"]) ?>"
                    >

                    <p class="categoria">
                        <?= htmlspecialchars($produto["categoria"]) ?>
                    </p>

                    <h3>
                        <?= htmlspecialchars($produto["nome"]) ?>
                    </h3>

                    <p class="preco">
                        <?= mostrarPreco($produto["preco"]) ?>
                    </p>

                    <a
                        class="botao"
                        href="?produto=<?= urlencode($produto["id"]) ?>#detalhes"
                    >
                        Ver produto
                    </a>

                </article>

            <?php endforeach; ?>

        </div>

    </section>

    <section class="sobre" id="sobre">

        <div class="container">

            <p class="subtitulo">QUEM SOMOS</p>

            <h2>Mais que roupas, um jeito de expressar quem você é.</h2>

            <p>
                A Urban Style reúne moda masculina e feminina
                com estilo, conforto e personalidade.
            </p>

        </div>

    </section>

<?php endif; ?>

</main>

<footer id="contato">

    <h2>URBAN STYLE</h2>

    <p>Seu estilo, sua identidade.</p>

    <p>© 2026 Urban Style. Todos os direitos reservados.</p>

</footer>

</body>
</html>