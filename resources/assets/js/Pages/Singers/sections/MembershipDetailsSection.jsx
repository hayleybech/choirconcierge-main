import CollapsePanel from '../../../components/CollapsePanel';
import { DetailList, DetailListItem } from '../components/DetailList';
import Badge from '../../../components/Badge';
import FeeStatus from '../../../components/FeeStatus';
import DateTag from '../../../components/DateTag';
import React from 'react';

export const MembershipDetailsSection = ({ singer }) => (
	<CollapsePanel>
		<DetailList>
			<DetailListItem label="Roles" colClass="sm:col-span-2">
				<div className="space-x-1.5 space-y-1.5">
					{singer.roles.map(role => (
						<Badge key={role.name}>{role.name.split(' ')[0]}</Badge>
					))}
				</div>
			</DetailListItem>
			<DetailListItem label="Membership Fees">
				<FeeStatus status={singer.fee_status} />
				{singer.paid_until && (
					<span className="text-sm text-gray-500 italic">
						<DateTag date={singer.paid_until} label="Expires" />
					</span>
				)}
			</DetailListItem>
			<DetailListItem label="Referred by">{singer.referrer ?? 'Unknown'}</DetailListItem>
			<DetailListItem label="Notes / Membership Details" colClass="sm:col-span-2">
				{singer.membership_details ?? 'N/A'}
			</DetailListItem>
			<DetailListItem label="Member Since">
				<DateTag date={singer.joined_at} />
				<br />
				<span className="text-sm text-gray-500 italic">
					<DateTag icon="pencil" date={singer.created_at} label="Membership Added" />
					<DateTag icon="pencil" date={singer.created_at} label="Membership Updated" />
				</span>
			</DetailListItem>
			<DetailListItem label="Last Login">
				<DateTag icon="sign-in" date={singer.user.last_login} />
			</DetailListItem>
			<DetailListItem label="Reason for Joining">{singer.reason_for_joining ?? 'Unknown'}</DetailListItem>
		</DetailList>
	</CollapsePanel>
);
