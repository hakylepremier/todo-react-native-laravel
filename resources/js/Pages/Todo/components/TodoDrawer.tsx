import React, { useEffect } from "react";
import {
    Box,
    Button,
    Checkbox,
    Drawer,
    FormControlLabel,
    List,
    Snackbar,
    Stack,
    TextField,
    Typography,
    useTheme,
} from "@mui/material";
import Divider from "@mui/material/Divider";
import IconButton from "@mui/material/IconButton";
import MenuIcon from "@mui/icons-material/Menu";
import ChevronLeftIcon from "@mui/icons-material/ChevronLeft";
import ChevronRightIcon from "@mui/icons-material/ChevronRight";
import ListItem from "@mui/material/ListItem";
import ListItemButton from "@mui/material/ListItemButton";
import ListItemIcon from "@mui/material/ListItemIcon";
import ListItemText from "@mui/material/ListItemText";
import InboxIcon from "@mui/icons-material/MoveToInbox";
import MailIcon from "@mui/icons-material/Mail";
import EventIcon from "@mui/icons-material/Event";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { LocalizationProvider } from "@mui/x-date-pickers/LocalizationProvider";
import { DatePicker } from "@mui/x-date-pickers/DatePicker";
import BookmarkBorderIcon from "@mui/icons-material/BookmarkBorder";
import BookmarkIcon from "@mui/icons-material/Bookmark";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import OutlinedFlagRoundedIcon from "@mui/icons-material/OutlinedFlagRounded";
import FlagRoundedIcon from "@mui/icons-material/FlagRounded";
import { PageProps, Todo } from "@/types";
import dayjs, { Dayjs } from "dayjs";
import { Link, useForm } from "@inertiajs/react";
import InputError from "@/Components/InputError";

type drawerClose = () => void;

const TodoDrawer = ({
    todo,
    openDrawer,
    handleDrawerClose,
}: PageProps<{
    todo: Todo | null;
    openDrawer: boolean;
    handleDrawerClose: drawerClose;
}>) => {
    const theme = useTheme();
    // const [open, setOpen] = React.useState(false);
    const [open, setOpen] = React.useState(openDrawer);
    const [date, setDate] = React.useState<string | null>(null);
    const [snackOpen, setSnackOpen] = React.useState(false);
    const [snackMessage, setSnackMessage] = React.useState("");
    const [dialogOpen, setDialogOpen] = React.useState(false);
    const [description, setDescription] = React.useState<string | null>(null);
    const [completed, setCompleted] = React.useState<boolean>(false);
    const [priority, setPriority] = React.useState<boolean>(false);
    const [due_date, setDueDate] = React.useState<string | undefined>(
        undefined
    );
    // const [todo, setTodo] = React.useState<Todo | null>(null);

    const {
        data,
        setData,
        patch,
        processing,
        reset,
        errors,
        delete: destroy,
    } = useForm<{
        description: string | null;
        completed: boolean;
        priority: boolean;
        due_date: string | undefined;
    }>({
        description: todo ? todo.description : null,
        completed: todo ? todo.completed : false,
        priority: todo ? todo.priority : false,
        due_date: todo ? todo.due_date : undefined,
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        console.log(e);
        setData("description", description);
        setData("completed", completed);
        setData("priority", priority);
        setData("due_date", due_date);
        patch(route("todos.update", todo?.id), {
            onSuccess: () => console.log("submitted"),
        });
    };

    const deleteTodo = () => {
        destroy(route("todos.update", todo?.id), {
            onSuccess: () => {
                setDialogOpen(false);
                drawerClose();
            },
        });
    };

    const toggleCompleted = (todoId: string) => () => {
        // const currentIndex = checked.indexOf(todoId);
        setData("completed", !data.completed);
        patch(route("todos.complete", todo?.id), {
            onSuccess: () => console.log("submitted"),
        });
    };

    const togglePriority = (todoId: string) => {
        setData("priority", !data.priority);
        patch(route("todos.priority", todo?.id), {
            onSuccess: () => {
                setSnackOpen(true);
                setSnackMessage("Priority updated");
                console.log("submitted");
            },
        });
    };

    const handleDialogClose = () => {
        setDialogOpen(false);
    };
    // const passTodo = (todo: Todo) => {
    //     setTodo(todo);
    //     setOpen(true);
    // };

    const handleDrawerOpen = () => {
        setOpen(true);
    };
    // const yes: Dayjs = dayjs(date).format("YYYY-MM-DD");
    const drawerClose = () => {
        setDescription(null);
        setCompleted(false);
        setPriority(false);
        setDueDate(undefined);
        handleDrawerClose();
        // openDrawer = false;
        // setOpen(false);
    };

    useEffect(() => {
        setDate(todo ? dayjs(todo.due_date).format("YYYY-MM-DD") : null);
        setDescription(todo ? todo.description : null);
        setCompleted(todo ? todo.completed : false);
        setPriority(todo ? todo.priority : false);
        setDueDate(todo ? todo.due_date : undefined);
        setData("description", todo ? todo.description : null);
        setData("due_date", todo ? todo.due_date : undefined);
        // setOpen(openDrawer);
    }, [todo]);
    return (
        <Drawer
            sx={{
                // width: drawerWidth,
                flexShrink: 0,
                // "& .MuiDrawer-paper": {
                //     width: drawerWidth,
                // },
            }}
            ModalProps={{
                keepMounted: false,
            }}
            variant="temporary"
            anchor="right"
            open={openDrawer}
        >
            <Box>
                {/* <IconButton onClick={handleDrawerClose}> */}
                <IconButton onClick={drawerClose}>
                    {theme.direction === "rtl" ? (
                        <ChevronLeftIcon />
                    ) : (
                        <ChevronRightIcon />
                    )}
                </IconButton>
            </Box>
            <Divider />
            <List>
                <form onSubmit={submit}>
                    {todo && (
                        // <ListItemButton>
                        //     <ListItemText primary={todo.description} />
                        // </ListItemButton>
                        <Box>
                            <ListItem
                                key={todo.id}
                                secondaryAction={
                                    <Checkbox
                                        edge="end"
                                        onChange={toggleCompleted(todo.id)}
                                        // onChange={toggleCompleted(todo.id)}
                                        checked={completed ? completed : false}
                                        inputProps={{
                                            "aria-labelledby": todo.id,
                                        }}
                                    />
                                }
                                divider
                            >
                                {/* <Box> */}
                                <TextField
                                    id="outlined-controlled"
                                    // label="Controlled"
                                    variant="standard"
                                    color="warning"
                                    multiline
                                    maxRows={4}
                                    // value={description ? description : null}
                                    value={data.description}
                                    fullWidth
                                    onChange={(
                                        e: React.ChangeEvent<HTMLInputElement>
                                    ) => {
                                        setData("description", e.target.value);
                                        setDescription(e.target.value);
                                    }}
                                    sx={{
                                        width: "200px",
                                        textDecoration: data.completed
                                            ? "line-through"
                                            : "none",
                                    }}
                                />
                                {/* <textarea
                                    value={
                                        description ? description : undefined
                                    }
                                    onChange={(
                                        e: React.ChangeEvent<HTMLTextAreaElement>
                                    ) => {
                                        setData("description", e.target.value);
                                        setDescription(e.target.value);
                                    }}
                                ></textarea> */}
                            </ListItem>
                            <ListItem>
                                <input
                                    type="date"
                                    name="due_date"
                                    id="due_date"
                                    // value={due_date ? due_date : undefined}
                                    value={data.due_date}
                                    onChange={(
                                        e: React.ChangeEvent<HTMLInputElement>
                                    ) => {
                                        setDueDate(e.target.value);
                                        setData("due_date", e.target.value);
                                    }}
                                />
                                <Button
                                    onClick={() => {
                                        setData("due_date", "");
                                    }}
                                >
                                    x
                                </Button>
                                {/* <LocalizationProvider
                                    dateAdapter={AdapterDayjs}
                                >
                                    <DatePicker
                                        value={data.due_date}
                                        onChange={(dueDate) => {
                                            // if (dueDate === null) {
                                            //     dueDate = undefined;
                                            // }
                                            const date = dueDate
                                                ? dueDate
                                                : undefined;
                                            setData("due_date", date);
                                        }}
                                    />
                                </LocalizationProvider> */}
                            </ListItem>

                            <ListItem>
                                <FormControlLabel
                                    value="start"
                                    control={
                                        <Checkbox
                                            edge="end"
                                            onChange={() =>
                                                togglePriority(todo.id)
                                            }
                                            icon={<OutlinedFlagRoundedIcon />}
                                            checkedIcon={<FlagRoundedIcon />}
                                            // onChange={toggleCompleted(todo.id)}
                                            aria-label="Yes"
                                            checked={data.priority}
                                            inputProps={{
                                                "aria-labelledby": todo.id,
                                            }}
                                        />
                                    }
                                    label="Start"
                                    labelPlacement="start"
                                />
                            </ListItem>
                            <ListItem>
                                <Stack direction={"row"} spacing={4}>
                                    <Button type="submit" variant="contained">
                                        Submit
                                    </Button>
                                    <Button
                                        color="error"
                                        onClick={() => setDialogOpen(true)}
                                    >
                                        Delete
                                    </Button>
                                </Stack>
                            </ListItem>
                        </Box>
                    )}
                </form>
            </List>
            <Divider />
            <InputError message={errors.description} className="mt-2" />
            <InputError message={errors.completed} className="mt-2" />
            <InputError message={errors.priority} className="mt-2" />
            <InputError message={errors.due_date} className="mt-2" />
            <Snackbar
                autoHideDuration={4000}
                open={snackOpen}
                // anchorOrigin={{ vertical, horizontal }}
                // variant={"outlined"}
                // color={"danger"}
                onClose={(event, reason) => {
                    // if (reason === "clickaway") {
                    //     return;
                    // }
                    setOpen(false);
                }}
            >
                <div>{snackMessage}</div>
            </Snackbar>
            <Dialog
                open={dialogOpen}
                onClose={handleDialogClose}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">
                    {"Are you sure you want to delete this todo?"}
                </DialogTitle>
                <DialogActions>
                    {/* {todo ? (
                        <Link
                            href={route("todos.destroy", todo.id)}
                            method="delete"
                        >
                            <div>Test</div>
                        </Link>
                    ) : (
                        <Box></Box>
                        )} */}
                    <Button onClick={deleteTodo}>Delete</Button>
                    <Button onClick={handleDialogClose} autoFocus>
                        Cancel
                    </Button>
                </DialogActions>
            </Dialog>
        </Drawer>
    );
};

export default TodoDrawer;
