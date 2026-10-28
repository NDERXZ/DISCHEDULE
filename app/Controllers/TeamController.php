<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\Team;
use App\Models\User;
use Core\Controller;

class TeamController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $teams = Team::getForUser($userId);

        $this->render('teams.index', [
            'title' => 'Teams & Shared Calendars - Dischedule',
            'teams' => $teams
        ], 'main');
    }

    public function store(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $input = $this->request->all();

        $this->validate($input, [
            'name' => 'required|max:100'
        ]);

        $teamId = Team::create([
            'owner_id'    => $userId,
            'name'        => trim((string)$input['name']),
            'description' => $input['description'] ?? null,
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        // Add owner as member with 'owner' role
        Team::addMember($teamId, $userId, 'owner');
        ActivityLog::log($userId, 'team_created', "Created team '{$input['name']}'.", null, $teamId);

        $this->flash('success', 'Team created successfully!');
        $this->redirect('/teams/' . $teamId);
    }

    public function show(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $team = Team::find($id);

        if (!$team || (!Team::isMember($id, $userId) && !$this->isAdmin())) {
            $this->flash('error', 'Team not found or access denied.');
            $this->redirect('/teams');
            return;
        }

        $members = Team::getMembers($id);
        $userRole = Team::userRole($id, $userId);

        $this->render('teams.show', [
            'title'    => $team['name'] . ' - Dischedule Team',
            'team'     => $team,
            'members'  => $members,
            'userRole' => $userRole
        ], 'main');
    }

    public function inviteMember(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $userRole = Team::userRole($id, $userId);

        if (!in_array($userRole, ['owner', 'admin'], true) && !$this->isAdmin()) {
            $this->flash('error', 'Only team managers can invite members.');
            $this->redirect('/teams/' . $id);
            return;
        }

        $input = $this->request->all();
        $this->validate($input, [
            'identifier' => 'required'
        ]);

        $identifier = trim((string)$input['identifier']);
        $targetUser = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? User::findByEmail($identifier)
            : User::findByUsername($identifier);

        if (!$targetUser) {
            $this->flash('error', "No user found with username/email '{$identifier}'.");
            $this->redirect('/teams/' . $id);
            return;
        }

        $role = in_array($input['role'] ?? '', ['admin', 'member'], true) ? $input['role'] : 'member';
        Team::addMember($id, (int)$targetUser['id'], $role);

        ActivityLog::log($userId, 'team_member_added', "Added {$targetUser['username']} to team #{$id}.", null, $id);

        $this->flash('success', "{$targetUser['username']} added to the team!");
        $this->redirect('/teams/' . $id);
    }

    public function removeMember(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $targetUserId = (int)$this->request->input('user_id');

        $userRole = Team::userRole($id, $userId);
        if ($targetUserId !== $userId && !in_array($userRole, ['owner', 'admin'], true) && !$this->isAdmin()) {
            $this->flash('error', 'Unauthorized.');
            $this->redirect('/teams/' . $id);
            return;
        }

        Team::removeMember($id, $targetUserId);
        ActivityLog::log($userId, 'team_member_removed', "Removed member #{$targetUserId} from team #{$id}.", null, $id);

        $this->flash('info', 'Member removed from team.');
        $this->redirect('/teams/' . $id);
    }

    public function destroy(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $team = Team::find($id);

        if (!$team || ((int)$team['owner_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Only team owner can delete the team.');
            $this->redirect('/teams');
            return;
        }

        Team::delete($id);
        ActivityLog::log($userId, 'team_deleted', "Deleted team #{$id}.");

        $this->flash('success', 'Team deleted.');
        $this->redirect('/teams');
    }
}
