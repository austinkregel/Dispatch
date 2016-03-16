<?php
/**
 * Created by PhpStorm.
 * User: sodium-chloride
 * Date: 3/16/2016
 * Time: 11:14 AM
 */

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\Dispatch\Commands\SendEmails;
use Kregel\Warden\Traits\Wardenable;
use Mail;

class TicketEdits extends Model
{

}