</main>
<!-- ========== END MAIN CONTENT ========== -->

<!-- JS Implementing Plugins -->
<script src="<?= base_url(); ?>assets/vendor/hs-toggle-password/dist/js/hs-toggle-password.js"></script>

<!-- JS Front -->
<script src="<?= base_url(); ?>assets/js/theme.min.js"></script>
<script src="<?= base_url(); ?>assets/vendor/hs-file-attach/dist/hs-file-attach.min.js"></script>
<script src="<?= base_url(); ?>assets/vendor/imask/dist/imask.min.js"></script>
<script src="<?= base_url(); ?>assets/vendor/hs-quantity-counter/dist/hs-quantity-counter.min.js"></script>
<!-- JS Plugins Init. -->
<script>
	(function () {
		// INITIALIZATION OF BOOTSTRAP VALIDATION
		// =======================================================
		HSBsValidation.init('.js-validate', {
			onSubmit: data => {
				$('button[type=submit]').prop("disabled", true);
				// add spinner to button
				$('button[type=submit]').html(
					`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
				);
				return;
			}
		})


		// INITIALIZATION OF TOGGLE PASSWORD
		// =======================================================
		new HSTogglePassword('.js-toggle-password');
		// INITIALIZATION OF FILE ATTACH
		// =======================================================
		new HSFileAttach('.js-file-attach')


		// INITIALIZATION OF INPUT MASK
		// =======================================================
		HSCore.components.HSMask.init('.js-input-mask')

		// INITIALIZATION OF  QUANTITY COUNTER
		// =======================================================
		new HSQuantityCounter('.js-quantity-counter')
	})()

</script>
</body>

</html>
