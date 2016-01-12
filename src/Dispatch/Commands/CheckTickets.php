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
    protected $name = 'dispatch:check-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for EternalTree migration & model install.';

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
        $this->checkTicketForDeadlinePriority();
        $this->info('Command dispatch:tickets fire');
    }

    public function checkTicketForDeadlinePriority()
    {
        $priorities = Priority::all();
        $tickets_today = $this->search(['created_at' => date('Y-m-d', strtotime('now')), 'priority_id' => ''], new Ticket());
    }

    private function search(Array $for, Model $in)
    {
        $results = [];
        foreach ($for as $where => $value) {
            $results[] = $in->where($where, 'like', $this->searchableWhere($value))->get();
        }

        return new Collection($results);
    }

    private function searchableWhere($where, $dontStrip = [])
    {
        return '%'.(str_replace(' ', '%',
            preg_replace('/[^a-z0-9'.implode('', $dontStrip).']+/', ' ', $where))).'%';
    }
}
