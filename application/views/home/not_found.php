<!DOCTYPE html>
<html lang="en" dir="" class="h-100">

<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title><?= ($this->uri->segment(1) ? ucwords(str_replace('-', ' ', $this->uri->segment(1)) . ' ' . ($this->uri->segment(2) ? str_replace('-', ' ', $this->uri->segment(2)) : "") . " - ".$web_title) : $web_title); ?></title>

	<meta name="description" content="<?= $web_desc; ?>">
	<meta property="og:title"
		content="<?= ($this->uri->segment(1) ? ucwords(str_replace('-', ' ', $this->uri->segment(1)) . ' ' . ($this->uri->segment(2) ? str_replace('-', ' ', $this->uri->segment(2)) : "") . $web_title) : $web_title); ?>">
	<meta property="og:description" content="<?= $web_desc; ?>">
	<meta property="og:image"
		content="<?= base_url(); ?>assets/images/<?= $web_icon?>">
	<meta property="og:url" content="<?= base_url(uri_string()) ?>">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/<?= $web_icon_white;?>">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/bootstrap-icons/font/bootstrap-icons.css">

    <!-- CSS Front Template -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/theme.min.css">
</head>

<body class="d-flex flex-column justify-content-center align-items-center h-100">
    <!-- ========== HEADER ========== -->
    <header id="header" class="navbar navbar-expand navbar-light navbar-absolute-top">
        <div class="container">
            <nav class="navbar-nav-wrap">
                <!-- Default Logo -->
                <a class="navbar-brand" href="<?= base_url(); ?>" aria-label="Front">
                    <img class="navbar-brand-logo" src="<?= base_url(); ?>assets/images/logo.png" alt="Logo">
                </a>
                <!-- End Default Logo -->
            </nav>
        </div>
    </header>
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <main id="content" role="main">
        <!-- Content -->
        <div class="container text-center">
            <div class="mb-3">
                <img class="img-fluid" src="<?= base_url(); ?>assets/svg/illustrations/oc-error.svg" alt="Image Description" style="width: 30rem;">
            </div>

            <div class="mb-4">
                <p class="fs-4 mb-0">Oops! Looks like you followed a bad link.</p>
                <p class="fs-4">If you think this is a problem with us, please <a class="link" href="mailto:ngodingin.indonesia@gmail.com">tell us</a>.</p>
            </div>

            <a class="btn btn-primary" href="<?= base_url(); ?>">Go back home</a>
        </div>
        <!-- End Content -->
    </main>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- ========== FOOTER ========== -->
    <footer class="position-absolute start-0 end-0 bottom-0">
        <div class="container pb-5 content-space-b-sm-1">
            <div class="row justify-content-between align-items-center">
                <div class="col-sm mb-3 mb-sm-0">
                    <p class="small mb-0">&copy; <?= $web_title;?> 2021 supported by Ngodingin Indonesia</p>
                </div>

                <div class="col-sm-auto">
                    <!-- Socials -->
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a class="btn btn-soft-secondary btn-xs btn-icon" href="<?= prep_url($web_facebook); ?>">
                                <i class="bi-facebook"></i>
                            </a>
                        </li>

                        <li class="list-inline-item">
                            <a class="btn btn-soft-secondary btn-xs btn-icon" href="<?= prep_url($web_instagram); ?>">
                                <i class="bi-instagram"></i>
                            </a>
                        </li>

                        <li class="list-inline-item">
                            <a class="btn btn-soft-secondary btn-xs btn-icon" href="<?= prep_url($web_twitter); ?>">
                                <i class="bi-twitter"></i>
                            </a>
                        </li>

                        <li class="list-inline-item">
                            <a class="btn btn-soft-secondary btn-xs btn-icon" href="<?= prep_url($web_youtube); ?>">
                                <i class="bi-youtube"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- End Socials -->
                </div>
            </div>
        </div>
    </footer>
    <!-- ========== END FOOTER ========== -->

    <!-- JS Global Compulsory  -->
    <script src="<?= base_url(); ?>assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS Front -->
    <script src="<?= base_url(); ?>assets/js/theme.min.js"></script>
</body>

</html>