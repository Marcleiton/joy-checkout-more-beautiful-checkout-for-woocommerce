<?php

namespace Joy_Checkout\Utils;

use Joy_Checkout\Common\Hooker_Trait;
use Joy_Checkout\Helpers as h;

class Asset_Manager {

	use Hooker_Trait;

	protected $global_dependencies = array();
	protected $enqueued            = array();

	public function add_hooks() {
		$this->add_action( 'wp_enqueue_scripts', 'enqueue_assets' );
		$this->add_action( 'admin_enqueue_scripts', 'enqueue_assets' );
	}

	public function add( $source, $args = array() ) {
		$type = h\get_file_extension( $source );

		$args        = \array_merge( $this->get_defaults(), $args );
		$args['src'] = $source;

		if ( empty( $args['handle'] ) ) {
			$args['handle'] = $args['prefix'] . \basename( $args['src'], ".$type" );
		}

		if ( empty( $args['deps'] ) ) {
			$args['deps'] = array();
		}

		$args['deps'] = \array_merge( $args['deps'], $this->get_global_dependencies( $type ) );

		if ( empty( $this->enqueued[ $type ] ) ) {
			$this->enqueued[ $type ] = array();
		}
		$this->enqueued[ $type ][] = $args;

		\do_action( h\prefix( 'added_asset' ), $args, $type );
	}

	public function get_enqueued( $type = 'js' ) {
		if ( empty( $this->enqueued[ $type ] ) ) {
			$this->enqueued[ $type ] = array();
		}

		return $this->enqueued[ $type ];
	}

	public function add_global_dependency( $handle, $type = 'js' ) {
		if ( empty( $this->global_dependencies[ $type ] ) ) {
			$this->global_dependencies[ $type ] = array();
		}
		$this->global_dependencies[ $type ][] = $handle;

		\do_action( h\prefix( 'added_global_asset_dependency' ), $handle );

		h\log_info( 'Added global ' . strtoupper( $type ) . ' dependency: handle=' . $args['handle'] . ' src=' . $args['src'] );
	}

	public function get_global_dependencies( $type = 'js' ) {
		if ( empty( $this->global_dependencies[ $type ] ) ) {
			$this->global_dependencies[ $type ] = array();
		}

		return $this->global_dependencies[ $type ];
	}

	public function enqueue_assets() {
		$in_admin = is_admin();
		$context  = $in_admin ? 'admin' : 'frontend';

		foreach ( $this->get_enqueued( 'js' ) as $args ) {
			if ( $in_admin !== $args['in_admin'] ) {
				continue;
			}

			if ( ! \is_callable( $args['condition'] ) || \call_user_func( $args['condition'] ) ) {
				\wp_enqueue_script(
					$args['handle'],
					$args['src'],
					$args['deps'],
					$args['version'],
					$args['in_footer']
				);

				h\log_info( "Enqueued JS ($context): handle=" . $args['handle'] . ' src=' . $args['src'] );

				$script_data = $args['script_data'];

				if ( ! empty( $script_data ) ) {
					$script_data_name = h\str_slug( $args['handle'], '_' ) . '_script_data';
					$script_data_name = \apply_filters( h\prefix( 'script_data_js_var' ), $script_data_name, $args );

					\wp_localize_script(
						$args['handle'],
						$script_data_name,
						$script_data
					);
				}
			}
		}

		foreach ( $this->get_enqueued( 'css' ) as $args ) {
			if ( $in_admin !== $args['in_admin'] ) {
				continue;
			}

			if ( ! \is_callable( $args['condition'] ) || \call_user_func( $args['condition'] ) ) {
				\wp_enqueue_style(
					$args['handle'],
					$args['src'],
					$args['deps'],
					$args['version'],
					$args['media']
				);
			}

			h\log_info( "Enqueued CSS ($context): handle=" . $args['handle'] . ' src=' . $args['src'] );
		}
	}

	public function get_defaults() {
		return array(
			'version'     => h\config_get( 'VERSION' ),
			'in_footer'   => true,
			'media'       => 'all',
			'in_admin'    => false,
			'condition'   => null,
			'prefix'      => h\prefix(),
			'script_data' => null,
		);
	}
}
