import React from 'react';
import Button from "../inputs/Button";
import LearningStatusTag from "./LearningStatusTag";
import SectionTitle from "../SectionTitle";
import SectionHeader from "../SectionHeader";

const MyLearningStatus = ({ song }) => (
    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <SectionHeader>
            <SectionTitle>My Learning Status</SectionTitle>
        </SectionHeader>

        <LearningStatusTag name={song.my_learning.status_name} colour={song.my_learning.status_colour} icon={song.my_learning.status_icon} />

        {song.my_learning.status === 'not-started' && (
            <Button href={route('songs.my-learning.update', song)} method="post" size="sm" className="mt-2">I'm Assessment Ready</Button>
        )}
    </div>
);

export default MyLearningStatus;