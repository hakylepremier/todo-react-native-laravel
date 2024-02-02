import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import { useForm } from "@inertiajs/react";
import { Stack, TextField } from "@mui/material";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { LocalizationProvider } from "@mui/x-date-pickers/LocalizationProvider";
import { DatePicker } from "@mui/x-date-pickers/DatePicker";
import React from "react";
import { DateTimePicker } from "@mui/x-date-pickers";
import dayjs from "dayjs";

const TodoForm = () => {
    const { data, setData, post, processing, reset, errors } = useForm<{
        description: String;
        completed: boolean;
        priority: boolean;
        due_date: String | null;
    }>({
        description: "",
        completed: false,
        priority: false,
        due_date: null,
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route("todos.store"), { onSuccess: () => reset() });
    };
    return (
        <form onSubmit={submit}>
            <Stack direction={"row"}>
                <TextField
                    id="outlined-controlled"
                    label="Controlled"
                    variant="standard"
                    color="warning"
                    value={data.description}
                    fullWidth
                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => {
                        setData("description", e.target.value);
                    }}
                />
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                    <DatePicker
                        value={data.due_date}
                        onChange={(due_date) => setData("due_date", due_date)}
                    />
                </LocalizationProvider>
                <InputError message={errors.description} className="mt-2" />
                <InputError message={errors.completed} className="mt-2" />
                <InputError message={errors.priority} className="mt-2" />
                <InputError message={errors.due_date} className="mt-2" />
                <PrimaryButton className="mt-4" disabled={processing}>
                    Submit
                </PrimaryButton>
            </Stack>
        </form>
    );
};

export default TodoForm;
