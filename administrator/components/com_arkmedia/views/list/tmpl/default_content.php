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

?>
<div id="<?php echo $this->css->content->containerid; ?>" class="<?php echo $this->css->content->container; ?>">
	<i class="<?php echo $this->css->bubble->mixed; ?>" <?php echo $this->data->bubble; ?>></i>
	<?php
		$this->event->trigger( 'onArkBeforeRenderTitle', array( &$this ) );

		echo Helper::layout( 'title', $this );

		$this->event->trigger( 'onArkAfterRenderTitle', array( &$this ) );
	?>
	<div id="<?php echo $this->css->content->topid; ?>" class="<?php echo $this->css->content->top; ?>">
		<div class="<?php echo $this->css->content->topbody; ?>">
			<div class="<?php echo $this->css->content->row; ?>">
				<div class="<?php echo $this->css->content->breadcrumb; ?>">
					<?php
						$this->event->trigger( 'onArkBeforeRenderBreadcrumbs', array( &$this ) );

						echo Helper::layout( 'breadcrumbs', $this );

						$this->event->trigger( 'onArkAfterRenderBreadcrumbs', array( &$this ) );
					?>
				</div>
				<div class="<?php echo $this->css->content->helpbar; ?>">
					<?php
						$this->event->trigger( 'onArkBeforeRenderHelpbar', array( &$this ) );

						echo Helper::layout( 'helpbar', $this );

						$this->event->trigger( 'onArkAfterRenderHelpbar', array( &$this ) );
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
		$this->event->trigger( 'onArkBeforeRenderEditbar', array( &$this ) );

		echo Helper::layout( 'edit.editbar', $this );

		$this->event->trigger( 'onArkAfterRenderEditbar', array( &$this ) );
	?>
	<div id="<?php echo $this->css->content->bottomid; ?>" class="<?php echo $this->css->content->bottom; ?>">
		<div class="<?php echo $this->css->content->bottombody; ?>">
			<?php
				$this->event->trigger( 'onArkBeforeRenderActions', array( &$this ) );

				echo Helper::layout( 'actions', $this );

				$this->event->trigger( 'onArkAfterRenderActions', array( &$this ) );
			?>
			<?php
				$this->event->trigger( 'onArkBeforeRenderSubActions', array( &$this ) );

				echo Helper::layout( 'subactions', $this );

				$this->event->trigger( 'onArkAfterRenderSubActions', array( &$this ) );
			?>
			<?php
				$this->event->trigger( 'onArkBeforeRenderMessage', array( &$this ) );

				echo Helper::layout( 'message.content', $this );

				$this->event->trigger( 'onArkAfterRenderMessage', array( &$this ) );
			?>
			<?php
				$this->event->trigger( 'onArkBeforeRenderFiles', array( &$this ) );

				echo Helper::layout( 'list.content.stacks', $this );

				$this->event->trigger( 'onArkAfterRenderFiles', array( &$this ) );
			?>
			<?php
				$this->event->trigger( 'onArkBeforeRenderEdit', array( &$this ) );

				echo Helper::layout( 'edit.container', $this );

				$this->event->trigger( 'onArkAfterRenderEdit', array( &$this ) );
			?>
		</div>
	</div>
</div>