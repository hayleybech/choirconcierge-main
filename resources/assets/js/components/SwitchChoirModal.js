import React, {useState} from "react";
import Dialog from "./Dialog";
import SingerSelect from "./inputs/SingerSelect";
import Label from "./inputs/Label";
import Select from "./inputs/Select";

const SwitchChoirModal = ({ isOpen, setIsOpen, choirs }) => {
    const [selectedChoir, setSelectedChoir] = useState(choirs[0].id);

    return (
        <Dialog
            title="Log in to another choir"
            okLabel="Log In"
            okUrl={selectedChoir ? route('tenants.switch.start', selectedChoir) : '#'}
            okVariant="primary"
            okMethod="get"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <p className="mb-2">
                All your choirs now share the same login details! Choose a choir below to switch.
            </p>

            <div className="mb-6">
                <Label label="Choirs" />
                <Select
                    name="login-choir"
                    options={choirs.map((choir) => ({ label: choir.choir_name, key: choir.id }))}
                    updateFn={(value) => setSelectedChoir(value)}
                />
            </div>

        </Dialog>
    );
}

export default SwitchChoirModal;