<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper;

defined( '_JEXEC' ) or die;

$this->event->trigger( 'onArkBeforeRenderFramework', array( &$this ) );

Helper::html( 'ark.bootstrap' );
Helper::html( 'ark.framework' );

$this->event->trigger( 'onArkAfterRenderFramework', array( &$this ) );

// Build Container Classes & Data Attributes
Helper::layout( 'css.layout', $this );

$this->event->trigger( 'onArkBeforeRenderJS', array( &$this ) );

echo Helper::layout( 'js.observer', $this );
echo Helper::layout( 'js.stack', $this );
echo Helper::layout( 'js.fx', $this );
echo Helper::layout( 'js.preloader', $this );
echo Helper::layout( 'js.resizer', $this );
echo Helper::layout( 'js.breadcrumb', $this );
echo Helper::layout( 'js.explorer', $this );
echo Helper::layout( 'js.title', $this );
echo Helper::layout( 'js.list', $this );
echo Helper::layout( 'js.edit', $this );
echo Helper::layout( 'js.thumbnail', $this );
echo Helper::layout( 'js.touch', $this );
echo Helper::layout( 'js.noscript', $this );

$this->event->trigger( 'onArkAfterRenderJS', array( &$this ) );

?>
<div id="<?php echo Helper::css(); ?>" class="<?php echo $this->css->root; ?>">
	<div class="<?php echo $this->css->container; ?>">
		<div class="<?php echo $this->css->top; ?>">
			<div class="<?php echo $this->css->toolbar; ?>">
				<?php
					$this->event->trigger( 'onArkBeforeRenderToolbar', array( &$this ) );

					echo Helper::layout( 'toolbar', $this );

					$this->event->trigger( 'onArkAfterRenderToolbar', array( &$this ) );
				?>
			</div>
		</div>
		<div class="<?php echo $this->css->bottom; ?>">
			<div class="<?php echo $this->css->left->mixed; ?>" <?php echo $this->data->left; ?>>
				<div class="<?php echo $this->css->row; ?>">
					<div class="<?php echo $this->css->navigation->mixed; ?>" <?php echo $this->data->navigation; ?>>
						<?php
							$this->event->trigger( 'onArkBeforeRenderNavigation', array( &$this ) );

							echo Helper::layout( 'navigation', $this );

							$this->event->trigger( 'onArkAfterRenderNavigation', array( &$this ) );
						?>
					</div>
					<div class="<?php echo $this->css->explorer->mixed; ?>" <?php echo $this->data->explorer; ?>>
						<?php
							$this->event->trigger( 'onArkBeforeRenderExplorer', array( &$this ) );

							echo Helper::layout( 'list.explorer.stacks', $this );

							$this->event->trigger( 'onArkAfterRenderExplorer', array( &$this ) );
						?>
					</div>
				</div>
			</div>
			<div class="<?php echo $this->css->right->mixed; ?>" <?php echo $this->data->right; ?>>
				<?php
					echo $this->loadTemplate( 'content' );
				?>
			</div>
		</div>
	</div>
</div>