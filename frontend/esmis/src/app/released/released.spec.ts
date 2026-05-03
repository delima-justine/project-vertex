import 'zone.js/testing';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';

import { Released } from './released';

describe('Released', () => {
  let component: Released;
  let fixture: ComponentFixture<Released>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Released],
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
        provideRouter([]),
      ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Released);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
