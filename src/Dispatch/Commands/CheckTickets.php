<?php

namespace Kregel\Dispatch\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kregel\Dispatch\Models\Priority;
use Kregel\Dispatch\Models\Ticket;

class CheckTickets extends Command implements SelfHandling
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dispatch:check-tickets' . ' {--fake= : Whether emails should be sent updating the user on the ticket statuses}';

    /**
     * This value is the difference between emailing all your clients at a random
     * time and just checking if the command works.
     * @var bool
     */
    protected $is_fake;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for checking all the tickets involved with our databases';


    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        switch (strtolower($this->option('fake'))) {
            case 'no':
            case 'false':
            case 'negative':
            case 'nope':
                $this->is_fake = false;
                break;
            case 'yea':
            case 'true':
            case 'yes':
                $this->is_fake = true;
                break;
            default:
                $this->is_fake = !config('mail.pretend');
        }
        $this->checkTicketForDeadlinePriority();
    }


    /**
     * @return void
     */
    public function checkTicketForDeadlinePriority()
    {
        $priorities    = Priority::all();
        $date          = date('Y-m-d', strtotime('now'));
        $tickets_month = Ticket::whereDate('finish_by', '<=', date('Y-m-d', strtotime('+1 month')))
            ->whereDate('finish_by', '>=', date('Y-m-d', strtotime('+1 week')) )->get();

        $tickets_week = Ticket::whereDate('finish_by', '<=', date('Y-m-d', strtotime('+1 week')) )->get();
        dd([
            'tickets this month, but not this week' =>$tickets_month,
            'tickets this week' => $tickets_week,
            'Now' => $date,
            'Now plus a week' => date('Y-m-d', strtotime('+1 week')),
            'is_fake' => $this->is_fake
        ]);
    }


    /**
     * @param array $for
     * @param Model $in
     * @param bool  $returnQuery
     *
     * @return Collection|QueryBuilder
     */
    private function search(array $for, Model $in, $returnQuery = false)
    {
        $results       = [ ];
        $queryBuilders = [ ];
        $query         = null;
        foreach ($for as $fields) {
            if ($query === null) {
                $query = $this->where($in, $fields);
            } else {
                $query = $this->where($query, $fields);
            }
        }
        if ($returnQuery) {
            return $query;
        }
        foreach ($queryBuilders as $query) {
            $results[] = $query->get();
        }

        return collect($results);
    }


    /**
     * @param $query
     * @param $where
     *
     * @return QueryBuilder
     */
    private function where($query, $where)
    {
        list( $field, $relation, $value ) = $where;

        return $query->where($field, $relation, $value);
    }


    /**
     * @param       $where
     * @param array $dontStrip
     *
     * @return string
     */
    private function searchableWhere($where, $dontStrip = [ ])
    {
        return '%' . ( str_replace(' ', '%', preg_replace('/[^a-z0-9' . implode('', $dontStrip) . ']+/', ' ', $where)) ) . '%';
    }
}
