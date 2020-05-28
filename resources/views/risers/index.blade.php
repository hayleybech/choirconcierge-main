@extends('layouts.page')

@section('title', 'Risers')
@section('page-title')
    <i class="fal fa-fw fa-folders"></i> Riser Placements
@endsection
@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Riser Placement</h3>

        <div class="card-body">
            <riser-stack></riser-stack>


            <!--
            <form id="riser_settings" class="form-inline">
                <div class="form-group">
                    <label for="riser_rows">Rows</label>
                    <input id="riser_rows" name="rows" type="number" min="1" max="8" class="form-control mx-3">
                </div>

                <div class="form-group">
                    <label for="riser_cols">Columns</label>
                    <input id="riser_cols" name="cols" type="number" min="1" max="8" class="form-control mx-3">
                </div>

                <div class="form-group">
                    <label for="riser_singers">Singers</label>
                    <input id="riser_singers" name="singers" type="number" min="1" max="150" class="form-control mx-3">
                </div>

                <div class="form-group">
                    <a href="#" id="riser_settings_submit" class="btn btn-primary">Update</a>
                </div>
            </form>
            -->

            <!--
            <div id="risers">
                <svg id="risers-frame" width="1000" height="500"></svg>
                <div id="risers-spots"></div>
            </div>-->

            <div class="row">

                <div class="col-md-3">
                    <holding-area title="Basses" part="bass" theme="primary" :singers="
                        [
                            {
                                id: 1,
                                email: 'abott@adorable.io',
                                name: 'abott',
                                part: 'bass'
                            },
                            {
                                id: 2,
                                email: 'bob@adorable.io',
                                name: 'bob',
                                part: 'bass'
                            },
                            {
                                id: 3,
                                email: 'carl@adorable.io',
                                name: 'carl',
                                part: 'bass'
                            },
                            {
                                id: 4,
                                email: 'john@adorable.io',
                                name: 'john',
                                part: 'bass',
                            },
                            {
                                id: 5,
                                email: 'andrew@adorable.io',
                                name: 'andrew',
                                part: 'bass'
                            },
                            {
                                id: 6,
                                email: 'matt@adorable.io',
                                name: 'matt',
                                part: 'bass'
                            }
                        ]"
                    ></holding-area>
                </div>

                <div class="col-md-3">
                    <holding-area title="Baritone" part="baritone" theme="success" :singers="
                        [
                            {
                                id: 7,
                                email: 'harry@adorable.io',
                                name: 'harry',
                                part: 'baritone'
                            },
                            {
                                id: 8,
                                email: 'red@adorable.io',
                                name: 'red',
                                part: 'baritone'
                            },
                            {
                                id: 9,
                                email: 'johnny@adorable.io',
                                name: 'johnny',
                                part: 'baritone'
                            },
                        ]"
                    ></holding-area>
                </div>

                <div class="col-md-3">
                    <holding-area title="Lead" part="lead" theme="danger" :singers="
                        [
                            {
                                id: 10,
                                email: 'harold@adorable.io',
                                name: 'harold',
                                part: 'lead'
                            },
                            {
                                id: 11,
                                email: 'joe@adorable.io',
                                name: 'joe',
                                part: 'baritone'
                            },
                        ]"
                    ></holding-area>
                </div>

                <div class="col-md-3">
                    <holding-area title="Tenor" part="tenor" theme="warning" :singers="
                        [
                            {
                                id: 12,
                                email: 'jack@adorable.io',
                                name: 'jack',
                                part: 'baritone'
                            },
                        ]"
                    ></holding-area>
                </div>

            </div>
        </div>

        <div class="card-footer">

        </div>

        <div class="card">
            <div class="card-body">
                <!--<ddtest></ddtest>-->
            </div>
        </div>

    </div>

@endsection