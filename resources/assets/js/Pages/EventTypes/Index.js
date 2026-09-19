import React, { useEffect, useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import EventTypeTableDesktop from './EventTypeTableDesktop';
import EventTypeTableMobile from './EventTypeTableMobile';
import { useForm, usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';
import Dialog from '../../components/Dialog';
import Form from '../../components/Form';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
import Error from '../../components/inputs/Error';
import DeleteDialog from '../../components/DeleteDialog';

const Index = ({ categories, setSidebarOpen }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	const [editingCategory, setEditingCategory] = useState(null);
	const [creatingCategory, setCreatingCategory] = useState(false);
	const [deletingCategory, setDeletingCategory] = useState(null);

	const breadcrumbs = [
		{ name: 'Events', url: route('events.index') },
		{ name: 'Event Types', url: route('event-types.index') },
	];

	return (
		<>
			<AppHead title="Event Types" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
					{can.create_event && (
						<PageActionsMenu>
							<ActionMenuItem onClick={() => setCreatingCategory(true)} variant="primary">
								<Icon icon="plus" mr />
								Add New
							</ActionMenuItem>
						</PageActionsMenu>
					)}
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="tags" type="solid" className="mr-2" />
						Event Types
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{can.create_event && (
						<Button onClick={() => setCreatingCategory(true)} size="sm" variant="primary">
							<Icon icon="plus" mr />
							Add New
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			{/* Desktop Table */}
			<div className="hidden lg:flex flex-col">
				<EventTypeTableDesktop
					categories={categories}
					showEditCategory={setEditingCategory}
					showDeleteCategory={setDeletingCategory}
				/>
			</div>

			{/* Mobile Table */}
			<div className="bg-white shadow block lg:hidden">
				<EventTypeTableMobile
					categories={categories}
					showEditCategory={setEditingCategory}
					showDeleteCategory={setDeletingCategory}
				/>
			</div>

			<EditCategoryDialog isOpen={!!editingCategory} setIsOpen={setEditingCategory} category={editingCategory} />
			<CreateCategoryDialog isOpen={creatingCategory} setIsOpen={setCreatingCategory} />

			<DeleteDialog
				title="Delete Event Type"
				url={deletingCategory ? route('event-types.destroy', { event_type: deletingCategory }) : '#'}
				isOpen={!!deletingCategory}
				setIsOpen={setDeletingCategory}
			>
				Are you sure you want to delete this event type? Related event will have this category removed. This
				action cannot be undone.
			</DeleteDialog>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;

const EditCategoryDialog = ({ isOpen, setIsOpen, category }) => {
	const { route } = useRoute();

	const { data, setData, post, errors } = useForm({
		_method: 'put',
		title: category?.title ?? '',
	});

	useEffect(() => {
		setData('title', category?.title ?? '');
	}, [category]);

	function submit(e) {
		e.preventDefault();

		post(route('event-types.update', { event_type: category }), {
			onSuccess: () => setIsOpen(false),
		});
	}

	return (
		<Dialog
			title="Edit Event Type"
			okLabel="Save"
			onOk={submit}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<Form onSubmit={submit}>
				<div className="flex flex-col gap-y-6">
					<div>
						<Label label="Title" forInput="title" />
						<TextInput
							name="title"
							value={data.title}
							updateFn={value => setData('title', value)}
							hasErrors={!!errors['title']}
						/>
						{errors.title && <Error>{errors.title}</Error>}
					</div>
				</div>
			</Form>
		</Dialog>
	);
};

const CreateCategoryDialog = ({ isOpen, setIsOpen }) => {
	const { route } = useRoute();

	const { data, setData, post, errors } = useForm({
		title: '',
	});

	function submit(e) {
		e.preventDefault();

		post(route('event-types.store'), {
			onSuccess: () => setIsOpen(false),
		});
	}

	return (
		<Dialog
			title="Create Event Type"
			okLabel="Save"
			onOk={submit}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<Form onSubmit={submit}>
				<div className="flex flex-col gap-y-6">
					<div>
						<Label label="Title" forInput="title" />
						<TextInput
							name="title"
							value={data.title}
							updateFn={value => setData('title', value)}
							hasErrors={!!errors['title']}
						/>
						{errors.title && <Error>{errors.title}</Error>}
					</div>
				</div>
			</Form>
		</Dialog>
	);
};
