import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackEditor from "../../components/RiserStack/RiserStackEditor";
import DateTag from "../../components/DateTag";
import DeleteDialog from "../../components/DeleteDialog";

const Show = ({ stack }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${stack.title} - Riser Stacks`} />
            <PageHeader
                title={stack.title}
                meta={[
                    <>Rows: {stack.rows}</>,
                    <>Columns: {stack.columns}</>,
                    <>Singers on front row: {stack.front_row_length}</>,
                    <>Front row on floor: {stack.front_row_on_floor ? 'Yes' : 'No'}</>,
                    <DateTag date={stack.created_at} label="Created" />,
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

            <DeleteDialog title="Delete Riser Stack" url={route('stacks.destroy', stack)} isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen}>
                Are you sure you want to delete this riser stack? This action cannot be undone.
            </DeleteDialog>

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
        </>
    );
}

Show.layout = page => <TenantLayout children={page} />

export default Show;