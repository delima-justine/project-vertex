import 'zone.js/testing';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';

import { CreateRequest } from './create-request';

describe('CreateRequest', () => {
  let component: CreateRequest;
  let fixture: ComponentFixture<CreateRequest>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateRequest],
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
        provideRouter([]),
      ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CreateRequest);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
