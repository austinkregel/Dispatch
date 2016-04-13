#What is this?
This currently is a support ticketing system that is under development. 

#What's with the commit comments?

So there are quite a few things that need to happen to make this usable.

First, if they're not registered, register the commands for Dispatch. Otherwise it won't be able to send out any emails.

Second, on the topic of emails, you need to have two queue listeners. One on the `default` queue, and another for `ticket-emails`.

Third, Give your user model the Dispatchable trait and publish the dispatch config.