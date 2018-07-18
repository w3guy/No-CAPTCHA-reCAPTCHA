<?php

if ( ! class_exists( 'MO_Admin_Notice' ) ) {

	class MO_Admin_Notice {
		public function __construct() {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'network_admin_notices', array( $this, 'admin_notice' ) );

			add_action( 'admin_post_mo_dismiss_adnotice', array( $this, 'dismiss_admin_notice' ) );
		}

		public function dismiss_admin_notice() {
			$url = admin_url();
			update_option( 'mo_dismiss_adnotice', 'true' );
			if ( isset( $_GET['url'] ) ) {
				$url = esc_url_raw( $_GET['url'] );
			}

			wp_redirect( $url );
			exit;
		}

		public function admin_notice() {

			if ( get_option( 'mo_dismiss_adnotice', 'false' ) == 'true' ) {
				return;
			}

			if ( $this->is_plugin_installed() && $this->is_plugin_active() ) {
				return;
			}

			$dismiss_url = esc_url_raw(
				add_query_arg(
					[
						'action' => 'mo_dismiss_adnotice',
						'url'    => $this->current_admin_url()
					],
					admin_url( 'admin-post.php' )
				)
			);
			$this->notice_css();
			$install_url = wp_nonce_url(
				admin_url( 'update.php?action=install-plugin&plugin=mailoptin' ),
				'install-plugin_mailoptin'
			);

			$activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=mailoptin%2Fmailoptin.php' ), 'activate-plugin_mailoptin/mailoptin.php' );
			?>
            <div class="mo-admin-notice notice notice-success">
                <div class="mo-notice-first-half">
                    <p>
						<?php
						printf(
							__( 'Powerful free plugin that %1$sconvert website visitors to email subscribers%2$s with beautiful conversion optimized forms%2$s and %1$sincrease revenue%2$s with automated newsletters.' ),
							'<span class="mo-stylize"><strong>', '</strong></span>' );
						?>
                    </p>
                    <p>
                        <iframe width="480" height="270" src="https://www.youtube.com/embed/Mix9_gTTlrE?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </p>
                </div>
                <div class="mo-notice-other-half">
					<?php if ( ! $this->is_plugin_installed() ) : ?>
                        <a class="button button-primary button-hero" id="mo-install-mailoptin-plugin" href="<?php echo $install_url; ?>">
							<?php _e( 'Install MailOptin Now for Free!' ); ?>
                        </a>
					<?php endif; ?>
					<?php if ( $this->is_plugin_installed() && ! $this->is_plugin_active() ) : ?>
                        <a class="button button-primary button-hero" id="mo-activate-mailoptin-plugin" href="<?php echo $activate_url; ?>">
							<?php _e( 'Activate MailOptin Now!' ); ?>
                        </a>
					<?php endif; ?>
                </div>
                <a href="<?php echo $dismiss_url; ?>">
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text"><?php _e( 'Dismiss this notice' ); ?>.</span>
                    </button>
                </a>
            </div>
			<?php
		}

		public function current_admin_url() {
			$parts = parse_url( home_url() );
			$uri   = $parts['scheme'] . '://' . $parts['host'];

			if ( array_key_exists( 'port', $parts ) ) {
				$uri .= ':' . $parts['port'];
			}

			$uri .= add_query_arg( [] );

			return $uri;
		}

		public function is_plugin_installed() {
			$installed_plugins = get_plugins();

			return isset( $installed_plugins['mailoptin/mailoptin.php'] );
		}

		public function is_plugin_active() {
			return is_plugin_active( 'mailoptin/mailoptin.php' );
		}

		public function notice_css() {
			?>
            <style type="text/css">
                .mo-admin-notice {
                    background: #0b11cfba;
                    color: #fff;
                    border-left-color: #000;
                    position: relative;
                }

                .mo-admin-notice .notice-dismiss:before {
                    color: #fff;
                }

                .mo-admin-notice .mo-stylize {
                    line-height: 2;
                    border-bottom: 2px solid #ff0412;
                }

                .mo-admin-notice .button-primary {
                    background: #ca4a1f;
                    text-shadow: none;
                    border: 0;
                    box-shadow: none;
                }

                .mo-notice-first-half {
                    width: 66%;
                    display: inline-block;
                }

                .mo-notice-other-half {
                    width: 33%;
                    display: inline-block;
                    padding: 15% 0;
                    position: absolute;
                }

                .mo-notice-first-half p {
                    font-size: 18px;
                }
            </style>
			<?php
		}

		public static function instance() {
			static $instance = null;

			if ( is_null( $instance ) ) {
				$instance = new self();
			}

			return $instance;
		}
	}
}

MO_Admin_Notice::instance();