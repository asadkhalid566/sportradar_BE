@extends('layout.index')

@section('content')
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
                    columns: [
                        { data: 'date', name: 'start_time' },
                        { data: 'title', name: 'title' },
                        { data: 'sport', name: 'sport.name' },
                        { data: 'location', name: 'location.name' },
                        { data: 'teams', orderable: false, searchable: false },
                        { data: 'actions', orderable: false, searchable: false }
                    ],
                    pageLength: 10,
                    order: [[0, 'asc']]
                });

                // Filter handling
                $('#applyFilters').on('click', () => table.ajax.reload());
                $('#resetFilters').on('click', function() {
                    $('#filter_sport').val('');
                    $('#filter_start').val('');
                    $('#filter_end').val('');
                    table.ajax.reload();
                });
                $('#filter_sport, #filter_start, #filter_end').on('change', () => table.ajax.reload());

                // Add Event
                $('#addEventForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);

                    // Clear old errors
                    form.find('.text-danger').text('');
                    form.find('.is-invalid').removeClass('is-invalid');

                    $.post("{{ route('events.store') }}", form.serialize())
                        .done(() => {
                            bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
                            form[0].reset();
                            table.ajax.reload(null, false);
                            toastr.success('✅ Event added successfully!');
                        })
                        .fail(xhr => {
                            if (xhr.responseJSON?.errors) {
                                $.each(xhr.responseJSON.errors, function(field, messages) {
                                    const input = form.find(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    form.find(`.error-${field}`).text(messages[0]);
                                });
                            }
                        });
                });

                // Edit Event (load data)
                $(document).on('click', '.btn-edit', function() {
                    $.get(`/events/${$(this).data('id')}/edit`, res => {
                        const e = res.event;
                        $('#edit_event_id').val(e.id);
                        $('#edit_title').val(e.title);
                        $('#edit_sport').val(e._sport_id);
                        $('#edit_location').val(e._location_id);
                        $('#edit_description').val(e.description);

                        const formattedDateTime = e.start_time.replace(' ', 'T').substring(0, 16);
                        $('#edit_start_time').val(formattedDateTime);
                        $('#edit_team1').val(res.teams[0] || '');
                        $('#edit_team2').val(res.teams[1] || '');

                        new bootstrap.Modal('#editEventModal').show();
                    });
                });

                // Update Event
                $('#editEventForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const id = $('#edit_event_id').val();

                    form.find('.text-danger').text('');
                    form.find('.is-invalid').removeClass('is-invalid');

                    $.post(`/events/${id}`, form.serialize() + '&_method=PUT')
                        .done(() => {
                            bootstrap.Modal.getInstance(document.getElementById('editEventModal')).hide();
                            form[0].reset();
                            table.ajax.reload(null, false);
                            toastr.success('✅ Event updated successfully!');
                        })
                        .fail(xhr => {
                            if (xhr.responseJSON?.errors) {
                                $.each(xhr.responseJSON.errors, function(field, messages) {
                                    const input = form.find(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    form.find(`.error-${field}`).text(messages[0]);
                                });
                            }
                        });
                });

                // Delete Event
                $(document).on('submit', '.deleteForm', function(e) {
                    e.preventDefault();
                    if (!confirm('Delete this event?')) return;

                    $.post(this.action, $(this).serialize() + '&_method=DELETE')
                        .done(() => table.ajax.reload(null, false))
                        .fail(() => toastr.error('Error deleting event.'));
                });

                // Show Event
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
                    }).fail(() => toastr.error('Error loading event details.'));
                });
            });
        </script>
    @endpush
@endsection
