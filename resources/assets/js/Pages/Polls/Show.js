import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import AppHead from '../../components/AppHead';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import useRoute from '../../hooks/useRoute';
import { useForm } from '@inertiajs/react';
import Button from '../../components/inputs/Button';
import FormWrapper from '../../components/FormWrapper';
import Form from '../../components/Form';
import FormSection from '../../components/FormSection';
import FormFooter from '../../components/FormFooter';
import Label from '../../components/inputs/Label';
import RadioGroup from '../../components/inputs/RadioGroup';
import { FancyCheckboxGroup } from '../../components/inputs/CheckboxGroup';
import Icon from '../../components/Icon';
import DateTag from '../../components/DateTag';
import Badge from '../../components/Badge';
import Prose from '../../components/Prose';

const Show = ({ poll, my_vote_option_ids = [], setSidebarOpen }) => {
	const { route } = useRoute();
	const { data, setData, post, processing, errors } = useForm({
		option_ids: my_vote_option_ids,
	});
	const isClosed = poll.is_closed;
	const actions = [
		{ label: 'Edit', url: route('polls.edit', { poll: poll.id }), icon: 'pencil', variant: 'secondary' },
		!isClosed && {
			label: 'Close Poll',
			url: route('polls.close', { poll: poll.id }),
			method: 'put',
			icon: 'lock',
			variant: 'secondary',
		},
		isClosed && {
			label: 'Re-open Poll',
			url: route('polls.open', { poll: poll.id }),
			method: 'put',
			icon: 'lock-open',
			variant: 'secondary',
		},
	].filter(Boolean);

	const toggle = id => {
		if (poll.can_vote_multiple) {
			setData(
				'option_ids',
				data.option_ids.includes(id) ? data.option_ids.filter(x => x !== id) : [...data.option_ids, id]
			);
		} else {
			setData('option_ids', [id]);
		}
	};

	const submit = e => {
		e.preventDefault();
		post(route('polls.vote', { poll: poll.id }));
	};

	return (
		<>
			<AppHead title={poll.title} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('polls.index')}
						breadcrumbs={[{ name: 'Polls', url: route('polls.index') }]}
					>
						<PageTopBarTitle title={poll.title} />
					</PageTopNavigation>
					<PageActionsMenu>
						{actions.map((action, key) => (
							<ActionMenuItem key={key} url={action.url} method={action.method} variant={action.variant}>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Polls', url: route('polls.index') },
									{ name: poll.title, url: route('polls.show', { poll: poll.id }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="poll" type="solid" className="mr-2" />
							{poll.title}
						</PageHeading>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							<div className="text-sm text-gray-600 flex gap-1">
								<span className="font-semibold">Status:</span>
								{poll.is_closed ? (
									<span className="text-gray-600 flex items-center">
										<Icon icon="lock" mr /> Closed
									</span>
								) : (
									<span className="text-emerald-700 flex items-center">
										<Icon icon="lock-open" mr /> Open
									</span>
								)}
							</div>
							<div className="text-sm text-gray-600">
								<span className="font-semibold">Deadline:</span>{' '}
								{poll.close_at ? new Date(poll.close_at).toLocaleString() : 'None'}
							</div>
							{poll.ensembles?.length > 0 && (
								<div className="text-sm text-gray-600 flex flex-wrap gap-1 items-center">
									<span className="font-semibold">Ensembles:</span>
									{poll.ensembles.map(ensemble => (
										<Badge key={ensemble.id} colour="bg-purple-100 text-purple-800">
											{ensemble.name}
										</Badge>
									))}
								</div>
							)}
							<DateTag
								icon="pencil"
								label="Created"
								date={poll.created_at}
								format="DATETIME_SHORT"
								className="text-gray-400"
							/>
							<DateTag
								icon="pencil"
								label="Updated"
								date={poll.updated_at}
								format="DATETIME_SHORT"
								className="text-gray-400"
							/>
						</div>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{actions.map(action => (
							<Button
								key={action.label}
								href={action.url}
								method={action.method}
								size="sm"
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</Button>
						))}
					</div>
				</div>
			</PageHeader2>

			<FormWrapper>
				<Form onSubmit={submit}>
					{poll.description && (
						<FormSection title="Description">
							<div className="sm:col-span-6 prose max-w-none">
								<Prose content={poll.description} />
							</div>
						</FormSection>
					)}
					<FormSection title="Cast your vote">
						<div className="sm:col-span-6">
							{poll.can_vote_multiple ? (
								<FancyCheckboxGroup
									name="recipient_roles"
									options={poll.options.map(opt => ({
										id: opt.id,
										name: opt.label,
										description: `${opt.votes_count ?? 0} votes`,
									}))}
									value={data.option_ids}
									onChange={value => toggle(value)}
									vertical
									disabled={isClosed}
								/>
							) : (
								<RadioGroup
									label={<Label label="Poll Options" />}
									options={poll.options.map(opt => ({
										id: opt.id,
										name: opt.label,
										description: `${opt.votes_count ?? 0} votes`,
									}))}
									selected={data.option_ids[0]}
									setSelected={value => toggle(value)}
									vertical
									contentVertical={false}
									disabled={isClosed}
								/>
							)}
						</div>
					</FormSection>

					<FormFooter className="flex justify-end">
						<Button type="submit" variant="primary" disabled={isClosed || processing}>
							{my_vote_option_ids.length > 0 ? 'Update Vote' : 'Submit Vote'}
						</Button>
					</FormFooter>
				</Form>
			</FormWrapper>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;
