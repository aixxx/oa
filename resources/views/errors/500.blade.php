@extends('errors::layout')

@section('title', 'Internal Server Error')

@section('message', $exception->getMessage())