import React, { useEffect, useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
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

	return (
		<>
			<AppHead title="Event Types" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('events.index')}
						breadcrumbs={[{ name: 'Events', url: route('events.index') }]}
					>
						<PageTopBarTitle title="Event Types" />
					</PageTopNavigation>
					{can.create_event && (
						<PageActionsMenu>
							<ActionMenuItem onClick={() => setCreatingCategory(true)} variant="primary">
								<Icon icon="plus" mr />
								Add New
							</ActionMenuItem>
						</PageActionsMenu>
					)}
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Events', url: route('events.index') },
									{ name: 'Event Types', url: route('event-types.index') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="tags" type="solid" className="mr-2" />
							Event Types
						</PageHeading>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{can.create_event && (
							<Button onClick={() => setCreatingCategory(true)} size="sm" variant="primary">
								<Icon icon="plus" mr />
								Add New
							</Button>
						)}
					</div>
				</div>
			</PageHeader2>

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
