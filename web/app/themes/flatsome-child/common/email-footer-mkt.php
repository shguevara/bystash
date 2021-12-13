<?php
defined( 'ABSPATH' ) || exit;

// Include Global Variables
include(get_template_directory() . '-child/common/global_variables.php');
?>
						</div>
						<!-- end: #body_content_inner -->
					</td>
				</tr>
			</table>
			<!-- End Content -->

            <?php
                 // Include pre-footer content
                // include(get_template_directory() . '-child/common/email-pre-footer.php');
				$awoo_unsubscribe_url = AW_Mailer_API::unsubscribe_url();
            ?>  

			<div class="footer-container">
                <p class="no-reply">Por favor, no respondas a este email ya que no vamos a poder responderte desde esta casilla. Si necesitas algo usa nuestra <a href="<?php echo $site_url ?>/contacto/" target="_blank">página de contacto</a>, vamos a estar encantados de ayudarte con lo que necesites!</p>
				<?php if ($awoo_unsubscribe_url != '') { ?> <p>No queremos que se corte pero sí ya no querés recibir emails promocionales nuestros hace <a href="<?php echo $awoo_unsubscribe_url ?>">clic acá</a> para actualizar tus preferencias.</p> <?php } ?>
				<p class="store-name"><?php echo get_option( 'woocommerce_email_footer_text' ) ; ?></p>
			</div>

		</div>
		<!-- end: #wrapper -->

	</body>
</html>


