import { PageProps, Todo } from "@/types";
import {
    Avatar,
    Box,
    Checkbox,
    List,
    ListItem,
    ListItemAvatar,
    ListItemButton,
    ListItemText,
    Stack,
    Typography,
} from "@mui/material";
import EventIcon from "@mui/icons-material/Event";
import React from "react";
import { useForm } from "@inertiajs/react";

type Pass = (todo: Todo) => void;

const TodoCard = ({
    todo,
    passTodo,
}: PageProps<{ todo: Todo; passTodo: Pass }>) => {
    const [checked, setChecked] = React.useState([1]);

    const { data, setData, patch, processing, reset, errors } = useForm<{
        completed: boolean;
    }>({
        completed: todo ? todo.completed : false,
    });

    const handleToggle = (todoId: string) => () => {
        // const currentIndex = checked.indexOf(todoId);
        setData("completed", !data.completed);
        patch(route("todos.complete", todo?.id), {
            onSuccess: () => console.log("submitted"),
        });
        const newChecked = [...checked];

        // if (currentIndex === -1) {
        //     newChecked.push(todo.id);
        // } else {
        //     newChecked.splice(currentIndex, 1);
        // }

        setChecked(newChecked);
    };
    const labelId = `checkbox-list-secondary-label-${todo.id}`;
    return (
        <ListItem
            key={todo.id}
            secondaryAction={
                <Checkbox
                    edge="end"
                    onChange={handleToggle(todo.id)}
                    checked={data.completed}
                    inputProps={{ "aria-labelledby": labelId }}
                />
            }
            disablePadding
            divider
        >
            <ListItemButton onClick={() => passTodo(todo)}>
                <Box>
                    {/* <ListItemText id={labelId} primary={todo.description} /> */}
                    <Typography
                        variant="body1"
                        pb={0}
                        sx={{
                            textDecoration: data.completed
                                ? "line-through"
                                : "none",
                        }}
                    >
                        {todo.description}
                    </Typography>
                    {todo.due_date && (
                        <Stack
                            direction={"row"}
                            alignItems={"center"}
                            spacing={1}
                        >
                            <EventIcon sx={{ fontSize: "12px" }} />
                            <Typography
                                variant="caption"
                                color="text.secondary"
                            >
                                {todo.due_date}
                            </Typography>
                        </Stack>
                    )}
                </Box>
            </ListItemButton>
        </ListItem>
    );
};

export default TodoCard;
