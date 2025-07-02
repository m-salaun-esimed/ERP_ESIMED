<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\OverdueInvoicesMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendOverdueInvoicesEmail extends Command
{
    protected $signature = 'invoices:send-overdue';

    protected $description = 'Send daily email to users with their overdue invoices';

    public function handle()
    {
        $users = User::with('customers.projects.quotes.invoices')->get();

        foreach ($users as $user) {
            $overdueInvoices = [];

            foreach ($user->customers as $customer) {
                foreach ($customer->projects as $project) {
                    foreach ($project->quotes as $quote) {
                        // boucle sur toutes les factures de la quote
                        foreach ($quote->invoices as $invoice) {
                            $dueDate = Carbon::parse($invoice->due_date);
                            $now = Carbon::now();

                            $this->info("Invoice {$invoice->invoice_number} due on {$dueDate}, now is {$now}");

                            if ($dueDate->lt($now) && $invoice->invoice_status_id != 3) {
                                $overdueInvoices[] = [
                                    'invoice' => $invoice,
                                    'customer' => $customer,
                                    'project' => $project,
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($overdueInvoices)) {
                foreach ($overdueInvoices as $item) {
                    $inv = $item['invoice'];
                    $this->info("Facture {$inv->invoice_number} due on {$inv->due_date} for user {$user->email}");
                }

                Mail::to($user->email)->send(new OverdueInvoicesMail($user, $overdueInvoices));
                $this->info("Mail sent to user {$user->email} with ".count($overdueInvoices)." overdue invoices.");
            } else {
                $this->info("No overdue invoices for user {$user->email}.");
            }
        }

        return 0;
    }

}
