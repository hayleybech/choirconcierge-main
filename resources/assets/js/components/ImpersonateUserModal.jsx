import React, {useState} from "react";
import Dialog from "./Dialog";
import SingerSelect from "./inputs/SingerSelect";
import Label from "./inputs/Label";
import useRoute from "../hooks/useRoute";

const ImpersonateUserModal = ({ isOpen, setIsOpen }) => {
    const { route } = useRoute();

    const [selectedSinger, setSelectedSinger] = useState(null);

    return (
        <Dialog
            title="Choose a user to impersonate"
            okLabel="Start"
            okUrl={selectedSinger ? route('users.impersonate', {user: selectedSinger}) : '#'}
            okVariant="primary"
            okMethod="get"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <p className="mb-2">
                With this feature, you can instantly log in as another user to use the site through their eyes.
                This is a great way to test the functionality and security of the site.
                Note that any changes you make as that user will be permanent, just as if that user had changed it.
            </p>
            <p className="mb-2">
                You can return to your account at anytime by opening the account menu then clicking "Stop Impersonating".
            </p>

            <div className="mb-6">
                <Label label="Singers" />
                <SingerSelect updateFn={(value) => setSelectedSinger(value)} />
            </div>

        </Dialog>
    );
}

export default ImpersonateUserModal;