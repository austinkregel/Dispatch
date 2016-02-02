<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDispatchTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        /*
         * jurisdictions have been moved to the users. Each user should have a required busines field.
         * Their Jurisdiciton requires them to only be able to view things from that business. no more
         * that.
         */

        Schema::create('dispatch_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title'); // This
            $table->text('body'); // This is the meat of the ticket.
            // This should be able to describe the whole ticket.

            // For who it's assigned to please check the dispatch_ticket_user database.
            $table->integer('jurisdiction_id')->unsigned();
            $table->integer('priority_id')->unsigned(); // To determin when things need to be completed

            // We need to be able to attach media to a ticket.
            $table->integer('owner_id')->unsigned(); // Need to know who made the ticket
            $table->integer('closer_id')->unsigned()->nullable(); // If the ticket is closed, who closed it.
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('dispatch_jurisdiction', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->string('phone_number');
            $table->timestamps();
        });

        Schema::create('dispatch_priority', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->string('deadline');
            $table->timestamps();
        });

        Schema::create('dispatch_jurisdiction_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('jurisdiction_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('dispatch_ticket_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->timestamps(); // For when it was assigned to the user.
        });
        Schema::create('dispatch_ticket_edits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->text('before');
            $table->text('after');
            $table->timestamps(); // For when it was assigned to the user.
        });
        Schema::create('dispatch_ticket_comments', function (Blueprint $table) {
            $table->text('body');
            $table->integer('user_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->timestamps(); // For when it was assigned to the user.
        });

        // This table will or at least should store info relating to the
        // media on the tickets.
        Schema::create('dispatch_ticket_media', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->integer('ticket_id')->unsigned();
            $table->integer('user_id')->unsigned();

            // This is the full path of any given media
            $table->text('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('dispatch_jurisdiction');
        Schema::drop('dispatch_jurisdiction_user');
        Schema::drop('dispatch_priority');
        Schema::drop('dispatch_ticket_user');
        Schema::drop('dispatch_tickets');
        Schema::drop('dispatch_ticket_media');
        Schema::drop('dispatch_ticket_comments');
    }
}
