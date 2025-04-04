<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold text-brand" href="/">Heroic</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item me-3"><a class="nav-link text-brand" href="/nextstep">What's Next</a></li>
                <li class="nav-item"><a class="nav-link" href="/docs"><i class="bi bi-journal-text me-1"></i>Docs</a></li>
                <li class="nav-item"><a class="nav-link" href="https://github.com/yllumi/heroic" target="_blank"><i class="bi bi-github me-1"></i>GitHub</a></li>
                
                <div x-data="themeToggle()" x-init="init()" class="d-flex align-items-center">
                    <button @click="toggle()" class="btn btn-link" title="Toggle theme">
                        <i :class="icon" class="bi"></i>
                    </button>
                </div>
            </ul>
        </div>
    </div>
</nav>