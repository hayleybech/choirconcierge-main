import React from 'react';
import Button from "../inputs/Button";
import LearningStatusTag from "./LearningStatusTag";
import SectionTitle from "../SectionTitle";
import SectionHeader from "../SectionHeader";
import LearningStatus from "../../LearningStatus";
import Prose from "../Prose";
import CollapsePanel from "../CollapsePanel";

const MyLearningStatus = ({ song }) => (
    <CollapsePanel>
        <LearningStatusTag status={new LearningStatus(song.my_learning.status)} />

        {song.my_learning.status === 'not-started' && (
            <Button href={route('songs.my-learning.update', song)} method="post" data={{ status: 'assessment-ready' }} size="sm" className="mt-2">I'm Assessment Ready</Button>
        )}
        {song.my_learning.status !== 'not-started' && (
            <Button href={route('songs.my-learning.update', song)} method="post" data={{ status: 'not-started' }} size="sm" className="mt-2">I'm Still Learning</Button>
        )}
    </CollapsePanel>
);

export default MyLearningStatus;