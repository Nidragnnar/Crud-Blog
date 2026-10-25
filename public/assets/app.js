// Fundo de estrelas animadas (<canvas class="estrelas">) e confirmação de formulários
const canvasEstrelas = document.querySelector(".estrelas");

if (canvasEstrelas) {
    const ctx = canvasEstrelas.getContext("2d");
    const semMovimento = matchMedia("(prefers-reduced-motion: reduce)").matches;
    let estrelas = [];

    const criarEstrelas = () => {
        const dpr = devicePixelRatio || 1;
        canvasEstrelas.width = innerWidth * dpr;
        canvasEstrelas.height = innerHeight * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        const quantidade = Math.round((innerWidth * innerHeight) / 4000);
        estrelas = Array.from({ length: quantidade }, () => ({
            x: Math.random() * innerWidth,
            y: Math.random() * innerHeight,
            raio: Math.random() * 1.3 + 0.2,
            velocidade: Math.random() * 0.15 + 0.02,
            fase: Math.random() * Math.PI * 2,
        }));
    };

    const desenharEstrelas = (tempo = 0) => {
        ctx.clearRect(0, 0, innerWidth, innerHeight);
        ctx.fillStyle = "#fff";
        for (const e of estrelas) {
            if (!semMovimento) {
                e.x -= e.velocidade;
                if (e.x < 0) e.x = innerWidth;
            }
            ctx.globalAlpha = semMovimento ? 0.8 : 0.5 + Math.sin(tempo / 700 + e.fase) * 0.4;
            ctx.beginPath();
            ctx.arc(e.x, e.y, e.raio, 0, Math.PI * 2);
            ctx.fill();
        }
        if (!semMovimento) requestAnimationFrame(desenharEstrelas);
    };

    criarEstrelas();
    desenharEstrelas();
    addEventListener("resize", () => {
        criarEstrelas();
        if (semMovimento) desenharEstrelas();
    });
}

// Pede confirmação antes de enviar formulários perigosos (ex.: apagar post)
document.querySelectorAll("form[data-confirmar]").forEach((form) => {
    form.addEventListener("submit", (evento) => {
        if (!confirm(form.dataset.confirmar)) evento.preventDefault();
    });
});
