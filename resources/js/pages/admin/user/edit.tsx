import React, { useEffect } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { SubmitHandler, useForm } from 'react-hook-form';
import { z } from "zod";
import { Button } from '@/components/ui/button';
import { zodResolver } from '@hookform/resolvers/zod';
import { toast } from "sonner";
import axios from 'axios';
import { RotateCw } from 'lucide-react';
import { cn } from '@/lib/utils';

const userSchema = z.object({
    firstName: z.string().min(3, { message: "First name must be at least 3 characters long" }),
    lastName: z.string().min(3, { message: "Last name must be at least 3 characters long" }),
    email: z.string().email(),
    userType: z.string({ message: "Please select user type" }),
    status: z.enum(["1", "0"], { message: "Please select status" }),
});

type FormFields = z.infer<typeof userSchema>;

type EditProps = {
    item: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
        user_type: string;
        status: boolean | number;
    };
};

export default function Edit({ item }: EditProps) {
    const {
        register,
        handleSubmit,
        setError,
        reset,
        formState: { errors, isSubmitting },
    } = useForm<FormFields>({
        resolver: zodResolver(userSchema),
    });

    useEffect(() => {
        reset({
            firstName: item.first_name,
            lastName: item.last_name,
            email: item.email,
            userType: item.user_type || "admin",
            status: item.status === true || item.status === 1 ? "1" : "0",
        });
    }, [item, reset]);

    const saveUser: SubmitHandler<FormFields> = async (data) => {
        try {
            const response = await axios.put(`/users/${item.id}`, data);
            if (response.data.success) {
                toast.success(response.data.message || "User updated successfully!");
            }
        } catch (error: any) {
            if (axios.isAxiosError(error)) {
                if (error.response?.data?.errors) {
                    const serverErrors = error.response.data.errors;
                    Object.keys(serverErrors).forEach((field) => {
                        setError(field as keyof FormFields, {
                            type: 'server',
                            message: serverErrors[field][0],
                        });
                    });
                    toast.error("Please fix the errors in the form");
                } else {
                    toast.error(error.response?.data?.message || "An error occurred");
                }
            } else {
                toast.error("An unexpected error occurred");
                console.error(error);
            }
        }
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Edit User', href: `/users/${item.id}/edit` }]}>
            <Head title="Edit User" />
            <div className="flex flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="border border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] md:w-3/4 flex-1 overflow-hidden rounded-xl md:min-h-min">
                    <div className="w-full p-5">
                        <form onSubmit={handleSubmit(saveUser)}>
                            {/* First Name */}
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="firstName">First Name</Label>
                                <Input
                                    {...register("firstName")}
                                    id="firstName"
                                    placeholder="First Name"
                                    className={cn(
                                        "w-full",
                                        errors.firstName && "border-red-500 focus-visible:ring-red-500"
                                    )}
                                />
                                {errors.firstName && (
                                    <span className="text-red-500">{errors.firstName.message}</span>
                                )}
                            </div>

                            {/* Last Name */}
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="lastName">Last Name</Label>
                                <Input
                                    {...register("lastName")}
                                    id="lastName"
                                    placeholder="Last Name"
                                    className={cn(
                                        "w-full",
                                        errors.lastName && "border-red-500 focus-visible:ring-red-500"
                                    )}
                                />
                                {errors.lastName && (
                                    <span className="text-red-500">{errors.lastName.message}</span>
                                )}
                            </div>

                            {/* Email */}
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    {...register("email")}
                                    id="email"
                                    placeholder="Email"
                                    className={cn(
                                        "w-full",
                                        errors.email && "border-red-500 focus-visible:ring-red-500"
                                    )}
                                />
                                {errors.email && (
                                    <span className="text-red-500">{errors.email.message}</span>
                                )}
                            </div>

                            {/* User Type */}
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="userType">User Type</Label>
                                <select
                                    {...register("userType")}
                                    id="userType"
                                    className={cn(
                                        "w-full rounded border border-input px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2",
                                        errors.userType && "border-red-500 ring-red-500"
                                    )}
                                >
                                    <option value="">Select user type</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                                {errors.userType && (
                                    <span className="text-red-500">{errors.userType.message}</span>
                                )}
                            </div>

                            {/* Status */}
                            <div className="mb-4 grid md:w-2/4 items-center gap-3">
                                <Label htmlFor="status">Status</Label>
                                <select
                                    {...register("status")}
                                    id="status"
                                    className={cn(
                                        "w-full rounded border border-input px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2",
                                        errors.status && "border-red-500 ring-red-500"
                                    )}
                                >
                                    <option value="">Select status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                {errors.status && (
                                    <span className="text-red-500">{errors.status.message}</span>
                                )}
                            </div>

                            {/* Submit Button */}
                            <div className="flex justify-end md:w-2/4">
                                <Button type="submit" disabled={isSubmitting} className="cursor-pointer">
                                    {isSubmitting ? (
                                        <>
                                            <RotateCw className="mr-2 h-4 w-4 animate-spin" />
                                            Processing...
                                        </>
                                    ) : (
                                        "Update"
                                    )}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
