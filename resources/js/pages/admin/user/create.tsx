import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Controller, SubmitHandler, useForm } from 'react-hook-form';
import {z} from "zod";
import { Button } from '@/components/ui/button';
import { zodResolver } from '@hookform/resolvers/zod';
import { toast } from "sonner"
import axios from 'axios';
import { RotateCw } from 'lucide-react';
import { cn } from '@/lib/utils';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from "@/components/ui/select";


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create User',
        href: '/users/create',
    },
];
const userSchema = z.object({
    firstName: z.string().min(3, { message: "First name must be at least 3 characters long" }),
    lastName: z.string().min(3, { message: "Last name must be at least 3 characters long" }),
    email: z.string().email(),
    userType: z.enum(["admin", "user"], { message: "Please select a user type" }),
});

type FormFields = z.infer<typeof userSchema>;


export default function Create() {
    const {
        register,
        control,        // Add control here
        handleSubmit,
        setError,
        reset,
        formState: { errors, isSubmitting },
    } = useForm<FormFields>({
        resolver: zodResolver(userSchema),
    });
    const saveUser: SubmitHandler<FormFields> = async (data) => {
        try {
            const response = await axios.post('/users', data);

            if (response.data.success) {
                // Reset form on success
                reset();
                toast.success(response.data.message);

                // Optional: handle response data
                // console.log('User created:', response.data);
            }


        } catch (error) {
            if (axios.isAxiosError(error)) {
                // Handle backend validation errors
                if (error.response?.data?.errors) {
                    // Set field-specific errors
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach((field) => {
                        setError(field as keyof FormFields, {
                            type: 'server',
                            message: errors[field][0] // Show first error for each field
                        });
                    });

                    // Show general error toast
                    toast.error("Please fix the errors in the form");
                } else {
                    // Show general API error
                    toast.error(error.response?.data?.message || "An error occurred");
                }
            } else {
                // Handle non-Axios errors
                toast.error("An unexpected error occurred");
                console.error('Error:', error);
            }
        }
    };
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create User" />
            <div className="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="bordred border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] md:w-3/4 flex-1 overflow-hidden rounded-xl border md:min-h-min">
                    <div className="w-full p-5">
                        <form action="" onSubmit={handleSubmit(saveUser)}>
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="firstName">First Name</Label>
                                <Input {...register("firstName")} className={cn(
                                    "w-full",
                                    errors.firstName && "border-red-500 focus-visible:ring-red-500" // Red border for error
                                )} type="text" id="firstName" placeholder="First Name" />
                                {errors.firstName && <span className="text-red-500">{errors.firstName.message}</span>}
                            </div>
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="lastName">Last Name</Label>
                                <Input {...register("lastName")} className={cn(
                                    "w-full",
                                    errors.lastName && "border-red-500 focus-visible:ring-red-500" // Red border for error
                                )} type="text" id="lastName" placeholder="Last Name" />
                                {errors.lastName && <span className="text-red-500">{errors.lastName.message}</span>}
                            </div>
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="email">Email</Label>
                                <Input {...register("email")} className={cn(
                                    "w-full",
                                    errors.email && "border-red-500 focus-visible:ring-red-500" // Red border for error
                                )} type="email" id="email" placeholder="Email" />
                                {errors.email && <span className="text-red-500">{errors.email.message}</span>}
                            </div>
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="userType">User Type</Label>
                                <Controller
                                    control={control}
                                    name="userType"
                                    defaultValue="admin"
                                    render={({ field }) => (
                                        <Select
                                            onValueChange={field.onChange}
                                            value={field.value}
                                            defaultValue=""
                                        >
                                            <SelectTrigger id="useType"
                                                           className={cn("w-full", errors.userType && "border-red-500 focus-visible:ring-red-500")}>
                                                <SelectValue placeholder="Select a user type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="admin">Admin</SelectItem>
                                                <SelectItem value="user">User</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    )}
                                />
                                {errors.userType && <span className="text-red-500">{errors.userType.message}</span>}
                            </div>
                            {errors.root && <p className="text-red-500 mb-4">{errors.root.message}</p>}
                            <div className="flex justify-end md:w-2/4">
                                <Button type="submit" className="cursor-pointer"
                                        disabled={isSubmitting}>{isSubmitting ? (
                                    <>
                                        <RotateCw className="mr-2 h-4 w-4 animate-spin" />
                                        Processing...
                                    </>
                                ) : (
                                    "Submit"
                                )}</Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
