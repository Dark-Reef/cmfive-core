<?php
function view_GET(Web $w) {
    $w->setLayout('layout-f6');

    list($task_id) = $w->pathMatch("id");
    $task = (!empty($task_id) ? $w->Task->getTask($task_id) : new Task($w));

    // Register for timelog if not new task
    if (!empty($task->id)) {
        $w->Timelog->registerTrackingObject($task);
    }

    if (!empty($task->id) && !$task->canView($w->Auth->user())) {
        $w->error("You do not have permission to edit this Task", "/task/tasklist");
    }

    // Get a list of the taskgroups and filter by what can be used
    $taskgroup_list = $w->Task->getTaskGroups();
    if (empty($taskgroup_list)) {
        if ((new Taskgroup($w))->canEdit($w->Auth->user())) {
            $w->msg('Please set up a taskgroup before continuing', '/task-group/viewtaskgrouptypes');
        } else {
            $w->error('There are no Tasks currently set up, please notify an Administrator', '/task');
        }
    }

    $taskgroups = array_filter($taskgroup_list, function($taskgroup){
        return $taskgroup->getCanICreate();
    });

    $tasktypes = array();
    $priority = array();
    $members = array();

    // Try and prefetch the taskgroup by given id
    $taskgroup = null;
    $taskgroup_id = $w->request("gid");
    $assignee_name = 0;
    if (!empty($taskgroup_id) || !empty($task->task_group_id)) {
        $taskgroup = $w->Task->getTaskGroup(!empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id);

        if (!empty($taskgroup->id)) {
            $tasktypes = $w->Task->getTaskTypes($taskgroup->task_group_type);
            $priority = $w->Task->getTaskPriority($taskgroup->task_group_type);
            $members = $w->Task->getMembersBeAssigned($taskgroup->id);
            sort($members);
            array_unshift($members,array("Unassigned","unassigned"));
        }
    }

    // Add history item
    if (empty($p['id'])) {
    	History::add("New Task");
    } else {
    	History::add("Task: {$task->title}", null, $task);
    }

    $status_list = Config::get("task." . $taskgroup->task_group_type)['statuses'];

    $w->ctx("t", (array)$task);
    $w->ctx("task", $task);
    $w->ctx("taskgroup_list", json_encode(array_map(function($task_group) {return ['value' => $task_group->id, 'text' => $task_group->title];}, $taskgroups)));
    $w->ctx("type_list", json_encode(array_map(function($tasktype) {return ['value' => $tasktype, 'text' => $tasktype];}, !empty($tasktypes[0]) ? $tasktypes[0] : [])));
    $w->ctx("status_list", json_encode(array_map(function($status) {return ['value' => $status[0], 'text' => $status[0]];}, $status_list)));
    $w->ctx("priority_list", json_encode(array_map(function($p) {return ['value' => $p[0], 'text' => $p[0]];}, $priority)));
    $w->ctx("assignee_list", json_encode(array_map(function($assignee) {return ['value' => $assignee[1], 'text' => $assignee[0]];}, $members)));
    $w->ctx("assignee_name", $task->getAssignee() ? $task->getAssignee()->getFullName(): null);
    $w->ctx("can_i_assign", $taskgroup->getCanIAssign());
    $w->ctx("subscribers", json_encode($task->getSubscribers()));
    $w->ctx('gravatar', 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($w->Auth->user()->getContact()->email))) . '?d=identicon&s=250');
    $w->ctx("taskgroup", $task->getTaskGroup());
    $w->ctx("title", 'View - ' . $task->title);
    //$w->ctx("canDelete", $task->canDelete($w->Auth->user()));
}