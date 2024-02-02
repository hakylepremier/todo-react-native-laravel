import React from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { PageProps, Todo } from "@/types";
import { Box, Button, Drawer, List, Stack, useTheme } from "@mui/material";
import TodoCard from "./components/TodoCard";
import TodoForm from "./components/TodoForm";
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
import TodoDrawer from "./components/TodoDrawer";

const drawerWidth = 240;

// type Pass = (todo: Todo) => void;

const Index = ({ auth, todos }: PageProps<{ todos: Todo[] }>) => {
    const theme = useTheme();
    const [open, setOpen] = React.useState(false);
    const [todo, setTodo] = React.useState<Todo | null>(null);
    const [key, setKey] = React.useState("start");

    const passTodo = (todo: Todo) => {
        setKey(todo.id);
        setTodo(todo);
        setOpen(true);
    };

    const handleDrawerOpen = () => {
        setOpen(true);
    };

    const handleDrawerClose = () => {
        setOpen(false);
    };
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Todo
                </h2>
            }
        >
            <Head title="Todo" />

            <Box>
                <div className="py-12">
                    {/* <div className="max-w-7xl mx-auto sm:px-6 lg:px-8"> */}
                    <div className="sm:px-6 lg:px-8">
                        <TodoForm />
                        <Button onClick={handleDrawerOpen}>Open</Button>
                        <List
                            sx={{
                                width: "100%",
                                // maxWidth: 360,
                                bgcolor: "background.paper",
                            }}
                        >
                            {todos.map((todo) => (
                                <TodoCard
                                    key={todo.id}
                                    auth={auth}
                                    todo={todo}
                                    passTodo={passTodo}
                                />
                            ))}
                        </List>
                    </div>
                </div>
                <TodoDrawer
                    key={key}
                    openDrawer={open}
                    auth={auth}
                    handleDrawerClose={handleDrawerClose}
                    todo={todo}
                />
                {/* <Drawer
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
                    open={open}
                >
                    <Box>
                        <IconButton onClick={handleDrawerClose}>
                            {theme.direction === "rtl" ? (
                                <ChevronLeftIcon />
                            ) : (
                                <ChevronRightIcon />
                            )}
                        </IconButton>
                    </Box>
                    <Divider />
                    <List>
                        <ListItem disablePadding>
                            {todo && (
                                <ListItemButton>
                                    <ListItemText primary={todo.description} />
                                </ListItemButton>
                            )}
                        </ListItem>
                        {["Inbox", "Starred", "Send email", "Drafts"].map(
                            (text, index) => (
                                <ListItem key={text} disablePadding>
                                    <ListItemButton>
                                        <ListItemIcon>
                                            {index % 2 === 0 ? (
                                                <InboxIcon />
                                            ) : (
                                                <MailIcon />
                                            )}
                                        </ListItemIcon>
                                        <ListItemText primary={text} />
                                    </ListItemButton>
                                </ListItem>
                            )
                        )}
                    </List>
                    <Divider />
                    <List>
                        {["All mail", "Trash", "Spam"].map((text, index) => (
                            <ListItem key={text} disablePadding>
                                <ListItemButton>
                                    <ListItemIcon>
                                        {index % 2 === 0 ? (
                                            <InboxIcon />
                                        ) : (
                                            <MailIcon />
                                        )}
                                    </ListItemIcon>
                                    <ListItemText primary={text} />
                                </ListItemButton>
                            </ListItem>
                        ))}
                    </List>
                </Drawer> */}
            </Box>
        </AuthenticatedLayout>
    );
};

export default Index;
