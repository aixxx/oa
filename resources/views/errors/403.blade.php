@extends('errors::layout')

@section('title', 'Forbidden')

@section('message', $exception->getMessage())