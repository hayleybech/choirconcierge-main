import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import {DateTime} from "luxon";
import Dialog from "../../components/Dialog";
import AppHead from "../../components/AppHead";
import RiserStackEditor from "../../components/RiserStack/RiserStackEditor";

const Show = ({ stack }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${stack.title} - Riser Stacks`} />
            <PageHeader
                title={stack.title}
                meta={[
                    <>
                        <i className="far fa-fw fa-calendar-day mr-1.5 text-gray-400 text-md" />
                        Created {DateTime.fromJSDate(new Date(stack.created_at)).toLocaleString(DateTime.DATE_MED)}
                    </>,
                    <>Rows: {stack.rows}</>,
                    <>Columns: {stack.columns}</>,
                    <>Singers on front row: {stack.front_row_length}</>,
                    <>Front row on floor: {stack.front_row_on_floor ? 'Yes' : 'No'}</>,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Riser Stacks', url: route('stacks.index')},
                    { name: stack.title, url: route('stacks.show', stack) },
                ]}
                actions={[
                    { label: 'Edit', icon: 'edit', url: route('stacks.edit', stack), can: 'update_stack' },
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_stack' },
                ].filter(action => action.can ? stack.can[action.can] : true)}
            />

            <DeleteStackDialog isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen} stack={stack} />

            <div className="bg-gray-50 flex-grow">
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 h-full">

                    <div className="sm:col-span-2">
                        <RiserStackEditor
                            rows={parseInt(stack.rows)}
                            columns={parseInt(stack.columns)}
                            spotsOnFrontRow={parseInt(stack.front_row_length)}
                            frontRowOnFloor={stack.front_row_on_floor}
                            singerPositions={stack.singers}
                            width={1000}
                            height={500}
                            setSelectedSinger={() => {}}
                            removeSingerFromHoldingArea={() => {}}
                            selectedSinger={null}
                            setPositions={() => {}}
                        />
                    </div>

                </div>
            </div>
        </>
    );
}

Show.layout = page => <Layout children={page} />

export default Show;

const DeleteStackDialog = ({ isOpen, setIsOpen, stack }) => (
    <Dialog
        title="Delete Riser Stack"
        okLabel="Delete"
        okUrl={route('stacks.destroy', stack)}
        okVariant="danger-solid"
        okMethod="delete"
        isOpen={isOpen}
        setIsOpen={setIsOpen}
    >
        <p>
            Are you sure you want to delete this riser stack?
            This action cannot be undone.
        </p>
    </Dialog>
);