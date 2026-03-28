import React, { useState } from 'react';
import Panel, { PanelTitle } from '../../components/Panel';
import { useForm } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';
import Button from '../../components/inputs/Button';
import RadioGroup from '../../components/inputs/RadioGroup';
import { FancyCheckboxGroup } from '../../components/inputs/CheckboxGroup';
import Icon from '../../components/Icon';
import Prose from "../../components/Prose";

const PollItem = ({ poll }) => {
	const { route } = useRoute();
	const { data, setData, post, processing } = useForm({
		option_ids: poll.my_vote_option_ids || [],
	});

	const [isExpanded, setIsExpanded] = useState(false);

	const isClosed = poll.is_closed;

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
		post(route('polls.vote', { poll: poll.id }), {
			preserveScroll: true,
		});
	};

	return (
		<div className="border-b last:border-b-0 pb-4 mb-4 last:pb-0 last:mb-0">
			<div className="flex justify-between items-start mb-2 gap-1">
				<h3 className="text-sm font-bold text-gray-900">
					{poll.title}
					<span className="ml-1 text-xs text-gray-500 font-normal">
						({poll.votes_count ?? 0} {poll.votes_count === 1 ? 'singer' : 'singers'} voted)
					</span>
				</h3>
				{poll.close_at && (
					<span className="text-xs text-gray-500 text-right">
						Closes: {new Date(poll.close_at).toLocaleDateString()}
					</span>
				)}
			</div>

			{poll.description && (
				<div className="mb-3">
					<div className={!isExpanded ? 'line-clamp-3' : ''}>
						<Prose content={poll.description} className="text-xs text-gray-600" />
					</div>
					{poll.description.length > 200 && (
						<button
							type="button"
							className="text-xs text-purple-600 hover:text-purple-800 font-bold mt-1"
							onClick={() => setIsExpanded(!isExpanded)}
						>
							{isExpanded ? 'Less' : 'More'}
						</button>
					)}
				</div>
			)}

			<form onSubmit={submit}>
				<div className="mb-3">
					{poll.can_vote_multiple ? (
						<FancyCheckboxGroup
							name={`poll_${poll.id}_options`}
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

				<div className="flex justify-end">
					<Button type="submit" variant="primary" size="xs" disabled={isClosed || processing}>
						{poll.my_vote_option_ids?.length > 0 ? 'Update Vote' : 'Submit Vote'}
					</Button>
				</div>
			</form>
		</div>
	);
};

const PollsWidget = ({ polls }) => {
	if (!polls || polls.length === 0) {
		return null;
	}

	const { route } = useRoute();

	return (
		<Panel
			header={
				<div className="flex justify-between items-center">
					<PanelTitle>
						<Icon icon="poll" mr />
						Active Polls <span className="text-xs text-gray-500 font-normal">(Latest 2)</span>
					</PanelTitle>

					<Button href={route('polls.index')} variant="secondary" size="xs">
						<Icon icon="list" style={{ lineHeight: '1rem' }} />
						<span className="hidden sm:inline">View All</span>
					</Button>
				</div>
			}
		>
			{polls.map(poll => (
				<PollItem key={poll.id} poll={poll} />
			))}
		</Panel>
	);
};

export default PollsWidget;
