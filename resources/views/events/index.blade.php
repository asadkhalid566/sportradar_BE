@extends('layout.index')

@section('content')
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2rem 0.6rem;
        }

        .dataTables_filter input {
            border-radius: 8px;
            padding: 5px 10px;
        }

        #applyFilters,
        #resetFilters {
            padding: 4px 10px;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Upcoming Sports Events</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal">
            + Add Event
        </button>
    </div>

     <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
        <div>
            <label class="form-label fw-semibold me-2 mb-0">Filter by Sport:</label>
            <select id="filter_sport" class="form-select form-select-sm d-inline-block w-auto">
                <option value="">All Sports</option>
                @foreach ($sports as $sport)
                    <option value="{{ $sport->name }}">{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label fw-semibold me-2 mb-0">From:</label>
            <input type="date" id="filter_start" class="form-control form-control-sm d-inline-block w-auto">
        </div>

        <div>
            <label class="form-label fw-semibold me-2 mb-0">To:</label>
            <input type="date" id="filter_end" class="form-control form-control-sm d-inline-block w-auto">
        </div>

        <button id="applyFilters" class="btn btn-sm btn-outline-primary ms-2">Apply</button>
        <button id="resetFilters" class="btn btn-sm btn-outline-secondary ms-1">Reset</button>
    </div>

    <div class="table-responsive">
        <table id="eventsTable" class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Date & Time</th>
                    <th>Title</th>
                    <th>Sport</th>
                    <th>Location</th>
                    <th>Teams</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

     @include('events.create', ['sports' => $sports, 'locations' => $locations, 'teams' => $teams])

     @include('events.edit', ['sports' => $sports, 'locations' => $locations, 'teams' => $teams])

     @include('events.show')

    @push('scripts')
        <script>
            $(function() {
                 const table = $('#eventsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('events.list') }}",
                        data: function(d) {
                            d.sport = $('#filter_sport').val();
                            d.start_date = $('#filter_start').val();
                            d.end_date = $('#filter_end').val();
                        }
                    },
                    columns: [{
                            data: 'date',
                            name: 'start_time'
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'sport',
                            name: 'sport.name'
                        },
                        {
                            data: 'location',
                            name: 'location.name'
                        },
                        {
                            data: 'teams',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ]
                });

                 $('#applyFilters').on('click', function() {
                    table.ajax.reload();
                });

                 $('#resetFilters').on('click', function() {
                    $('#filter_sport').val('');
                    $('#filter_start').val('');
                    $('#filter_end').val('');
                    table.ajax.reload();
                });

                 $('#filter_sport, #filter_start, #filter_end').on('change', function() {
                    table.ajax.reload();
                });

                 $('#addEventForm').on('submit', function(e) {
                    e.preventDefault();
                    $.post("{{ route('events.store') }}", $(this).serialize())
                        .done(() => {
                            bootstrap.Modal.getInstance('#addEventModal').hide();
                            this.reset();
                            alert('Event added!');
                            table.ajax.reload(null, false);
                        })
                        .fail(xhr => alert(Object.values(xhr.responseJSON.errors).flat().join("\n")));
                });


                 $(document).on('click', '.btn-edit', function() {
                    $.get(`/events/${$(this).data('id')}/edit`, res => {
                        const e = res.event;
                        $('#edit_event_id').val(e.id);
                        $('#edit_title').val(e.title);
                        $('#edit_sport').val(e._sport_id);
                        $('#edit_location').val(e._location_id);
                        $('#edit_description').val(e.description);

                         let formattedDateTime = e.start_time
                            .replace(' ', 'T')  
                            .substring(0, 16);  

                        $('#edit_start_time').val(formattedDateTime);

                         $('#edit_team1').val(res.teams[0] || '');
                        $('#edit_team2').val(res.teams[1] || '');

                        new bootstrap.Modal('#editEventModal').show();
                    });
                });


                 $('#editEventForm').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#edit_event_id').val();
                    $.post(`/events/${id}`, $(this).serialize() + '&_method=PUT')
                        .done(() => {
                            bootstrap.Modal.getInstance('#editEventModal').hide();
                            this.reset();
                            alert('✅ Event updated!');
                            table.ajax.reload(null, false);
                        })
                        .fail(xhr => alert(Object.values(xhr.responseJSON.errors).flat().join("\n")));
                });

                 $(document).on('submit', '.deleteForm', function(e) {
                    e.preventDefault();
                    if (!confirm('Delete this event?')) return;
                    $.post(this.action, $(this).serialize() + '&_method=DELETE')
                        .done(() => table.ajax.reload(null, false))
                        .fail(() => alert('Error deleting event.'));
                });

                 $(document).on('click', '.btn-show', function() {
                    const id = $(this).data('id');
                    $.get(`/events/${id}`, function(res) {
                        const e = res.event;
                        $('#show_title').text(e.title);
                        $('#show_sport').text(e.sport);
                        $('#show_location').text(e.location);
                        $('#show_teams').text(e.teams);
                        $('#show_start_time').text(e.start_time);
                        $('#show_description').text(e.description);
                        new bootstrap.Modal('#showEventModal').show();
                    }).fail(() => alert(' Error loading event details.'));
                });
            });
        </script>
    @endpush
@endsection
