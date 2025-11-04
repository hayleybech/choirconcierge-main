import CollapsePanel from '../../../components/CollapsePanel';
import { DetailList, DetailListItem } from '../components/DetailList';
import Icon from '../../../components/Icon';
import DateTag from '../../../components/DateTag';
import React from 'react';

export const PersonalDetailsSection = ({ singer }) => (
	<CollapsePanel>
		<DetailList>
			<DetailListItem label="Contact Details" colClass="sm:col-span-2 xl:col-span-1">
				<p>
					<Icon icon="envelope" mr type="regular" className="text-gray-400" />
					<a href={`mailto:${singer.user.email}`} target="_blank">
						{singer.user.email}
					</a>
				</p>
				<p>
					<Icon icon="phone" mr type="regular" className="text-gray-400" />
					{singer.user.phone ? (
						<a href={`tel:${singer.user.phone}`} target="_blank">
							{singer.user.phone}
						</a>
					) : (
						'No phone'
					)}
				</p>
			</DetailListItem>
			<DetailListItem label="Date of Birth">
				{singer.user.dob ? <DateTag date={singer.user.dob} /> : 'No date of birth'}
			</DetailListItem>
			<DetailListItem label="Height">
				{singer.user.height ? `${Math.round(singer.user.height)} cm` : 'Unknown'}
			</DetailListItem>
			<DetailListItem label="BHA Member ID">
				<Icon icon="id-card" mr type="regular" className="text-gray-400" />
				<span>{singer.user.bha_id ?? 'Unknown'}</span>
				<span className="ml-2 text-gray-500">{`(${singer.user.bha_type})` ?? ''}</span>
			</DetailListItem>
			<DetailListItem label="Address">
				{singer.user.address_street_1 ? (
					<>
						{singer.user.address_street_1}
						<br />
						{singer.user.address_street_2 && (
							<>
								{singer.user.address_street_2}
								<br />
							</>
						)}
						{`${singer.user.address_suburb}, ${singer.user.address_state} ${singer.user.address_postcode}`}
					</>
				) : (
					'No address'
				)}
			</DetailListItem>
			<DetailListItem label="Profession">{singer.user.profession ?? 'None listed'}</DetailListItem>
			<DetailListItem label="Other Skills">{singer.user.skills ?? 'None listed'}</DetailListItem>
			<DetailListItem label="Emergency Contact" colClass="sm:col-span-2 xl:col-span-1">
				<p>
					<Icon icon="user" mr type="regular" className="text-gray-400" />
					{singer.user.ice_name ?? 'No emergency contact'}
				</p>
				<p>
					<Icon icon="phone" mr type="regular" className="text-gray-400" />
					{singer.user.ice_phone ? (
						<a href={`tel:${singer.user.ice_phone}`} target="_blank">
							{singer.user.ice_phone}
						</a>
					) : (
						'No phone'
					)}
				</p>
			</DetailListItem>
			<DetailListItem label="Dietary Requirements">
				{singer.user.dietary_requirements ?? 'None listed'}
			</DetailListItem>
			<DetailListItem label="Medical Conditions">
				{singer.user.medical_conditions ?? 'None listed'}
			</DetailListItem>
			<DetailListItem>
				<span className="text-sm text-gray-500 italic">
					<DateTag icon="pencil" date={singer.user.created_at} label="Profile Created" />
					<DateTag icon="pencil" date={singer.user.created_at} label="Profile Updated" />
				</span>
			</DetailListItem>
		</DetailList>
	</CollapsePanel>
);
