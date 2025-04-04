<div id="home" class="d-flex align-items-center justify-content-center py-5" style="min-height:100vh">

  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-5 text-center">
        <img src="/vendor/heroic/logo.png" alt="Heroic Logo" class="mb-4" style="max-width:70%;" />
      </div>

      <div class="col-md-7 text-center text-md-start">
        <h1 class="display-4 fw-bold">Welcome to <span style="color:var(--heroic-orange);">Heroic</span></h1>
        <p class="lead text-muted">
        Heroic, as a metaframework for CodeIgniter 4, offers a solution 
        for web developers to build progressive web applications more quickly 
        and efficiently. Create your next masterpiece with Heroic!
        </p>

        <div class="mt-4 d-flex gap-3 flex-wrap justify-content-center">
          <a href="/whatsnext" class="btn btn-primary">
            <i class="bi bi-arrow-return-right me-2"></i> What's Next?
          </a>
          <a href="https://yllumi.github.io/heroic/intro" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-journal-text me-2"></i> Docs
          </a>
          <a href="https://github.com/yllumi/heroic" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-github me-2"></i> GitHub
          </a>

          <div x-data="themeToggle()" x-init="init()" class="d-flex align-items-center">
            <button @click="toggle()" class="btn btn-outline-secondary" title="Toggle theme">
              <i :class="icon" class="bi"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>