<?php
/**
 * @version     2.5.x
 * @package     com_improvemycity
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      Ioannis Tsampoulatidis for the URENIO Research Unit
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Improvemycity.
 */
class ImprovemycityViewIssues extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'improvemycity.php';

		$state	= $this->get('State');
		$canDo	= ImprovemycityHelper::getActions($state->get('filter.category_id'));
		
		JToolBarHelper::title(JText::_('COM_IMPROVEMYCITY_TITLE_ITEMS'), 'items.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'issue';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('issue.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit')) {
			    JToolBarHelper::editList('issue.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('issues.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('issues.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'issues.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('issues.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('issues.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'issues.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('issues.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_improvemycity');
		}


	}
}
