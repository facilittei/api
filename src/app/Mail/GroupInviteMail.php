<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\GroupInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GroupInviteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The group.
     *
     * @var \App\Models\Group
     */
    public $group;

    /**
     * The invite.
     *
     * @var \App\Models\GroupInvite
     */
    public $invite;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Group $group, GroupInvite $invite)
    {
        $this->group = $group;
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('mail.groups_subject'))->markdown('mail.groups.invite');
    }
}
